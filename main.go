package main

import (
	"compress/gzip"
	"context"
	"fmt"
	"io"
	"net"
	"net/http"
	"net/url"
	"strings"
	"sync"
	"time"
)

// Proxy representa la configuración de un proxy
type Proxy struct {
	Host string
	Port string
	User string
	Pass string
}

// decodeChapiDirectCall verifica si la respuesta está bloqueada
func decodeChapiDirectCall(response string) string {
	// Verificar si la respuesta está bloqueada
	if strings.Contains(response, "item cannot be shipped") {
		return "unauthorized"
	}

	if strings.Contains(response, "To discuss automated") {
		return "unauthorized"
	}

	// Limpiar la respuesta si es necesario
	cleanResponse := strings.ReplaceAll(response, "\r", "")
	cleanResponse = strings.ReplaceAll(cleanResponse, "\n", "")
	cleanResponse = strings.ReplaceAll(cleanResponse, "&&&", ",")

	return cleanResponse
}

// fetchURL realiza una solicitud HTTP a través de un proxy utilizando context para cancelación
func fetchURL(ctx context.Context, targetURL string, proxy Proxy, headers map[string]string, wg *sync.WaitGroup, resultChan chan<- string) {
	defer wg.Done()

	// Configurar el proxy
	proxyURL := fmt.Sprintf("http://%s:%s@%s:%s", proxy.User, proxy.Pass, proxy.Host, proxy.Port)
	proxyParsed, err := url.Parse(proxyURL)
	if err != nil {
		fmt.Printf("Error parseando proxy %s: %v\n", proxy.Host, err)
		return
	}

	// Crear un transporte con proxy y timeout personalizado
	transport := &http.Transport{
		Proxy: http.ProxyURL(proxyParsed),
		// Puedes ajustar los timeouts de conexión, por ejemplo:
		DialContext: (&net.Dialer{
			Timeout:   10 * time.Second,
			KeepAlive: 30 * time.Second,
		}).DialContext,
	}

	// Crear un cliente HTTP con el transporte y timeout general
	client := &http.Client{
		Transport: transport,
		Timeout:   15 * time.Second, // Timeout global para la solicitud
	}

	// Crear la solicitud HTTP y adjuntar el context para poder cancelarla
	req, err := http.NewRequestWithContext(ctx, "GET", targetURL, nil)
	if err != nil {
		fmt.Printf("Error creando la solicitud para el proxy %s: %v\n", proxy.Host, err)
		return
	}

	// Agregar los headers personalizados
	for key, value := range headers {
		req.Header.Add(key, value)
	}

	// Realizar la solicitud HTTP
	resp, err := client.Do(req)
	if err != nil {
		// Si el error es por contexto cancelado, no se imprime para evitar ruido
		if ctx.Err() != nil {
			return
		}
		fmt.Printf("Error con el proxy %s: %v\n", proxy.Host, err)
		return
	}
	defer resp.Body.Close()

	// Leer la respuesta
	var body []byte
	if strings.Contains(resp.Header.Get("Content-Encoding"), "gzip") {
		reader, err := gzip.NewReader(resp.Body)
		if err != nil {
			fmt.Printf("Error descomprimiendo la respuesta del proxy %s: %v\n", proxy.Host, err)
			return
		}
		defer reader.Close()
		body, err = io.ReadAll(reader)
		if err != nil {
			fmt.Printf("Error leyendo la respuesta descomprimida del proxy %s: %v\n", proxy.Host, err)
			return
		}
	} else {
		body, err = io.ReadAll(resp.Body)
		if err != nil {
			fmt.Printf("Error leyendo la respuesta del proxy %s: %v\n", proxy.Host, err)
			return
		}
	}

	response := string(body)
	decodedResponse := decodeChapiDirectCall(response)
	if decodedResponse == "unauthorized" {
		fmt.Printf("Respuesta bloqueada desde el proxy %s\n", proxy.Host)
		return
	}

	// Intentar enviar la respuesta al canal sin bloquear (ya que puede haberse cancelado)
	select {
	case resultChan <- decodedResponse:
	default:
	}
}

func scrapeHandler(w http.ResponseWriter, r *http.Request) {
	targetURL := r.URL.Query().Get("url")
	if targetURL == "" {
		http.Error(w, "Falta el parámetro 'url'", http.StatusBadRequest)
		return
	}

	cookie := r.URL.Query().Get("cookie")
	userAgent := r.URL.Query().Get("userAgent")

	// Headers personalizados
	headers := map[string]string{
		"Connection":       "keep-alive",
		"Accept":           "*/*",
		"Content-Language": "es-US",
		"User-Agent":       userAgent,	
	}

	if cookie != "" {
		headers["Cookie"] = cookie
	}

	// Lista de proxies
	proxies := []Proxy{
		{Host: "dc.oxylabs.io", Port: "8000", User: "user-chupaprecios_lDWEa-country-US", Pass: "+Aq1w2e3r4t5"},
		{Host: "pr.oxylabs.io", Port: "7777", User: "customer-jotapey3_qcf4a-cc-us", Pass: "+Aq1w2e3r4t5"},
	}

	// Crear un contexto con cancelación para abortar las demás solicitudes
	ctx, cancel := context.WithCancel(context.Background())
	defer cancel()

	resultChan := make(chan string, 1)
	var wg sync.WaitGroup

	// Lanzar una goroutine por cada proxy
	for _, proxy := range proxies {
		wg.Add(1)
		go fetchURL(ctx, targetURL, proxy, headers, &wg, resultChan)
	}

	// Goroutine para cerrar el canal una vez que todas las goroutines terminen
	go func() {
		wg.Wait()
		close(resultChan)
	}()

	// Esperar la primera respuesta válida
	var response string
	select {
	case response = <-resultChan:
		// Cancelar las demás peticiones en cuanto se reciba una respuesta
		cancel()
	case <-time.After(20 * time.Second):
		// Timeout global en caso de que ninguna petición responda a tiempo
		http.Error(w, "Timeout en la solicitud", http.StatusRequestTimeout)
		return
	}

	if response == "" {
		http.Error(w, "Todos los proxies fallaron o fueron bloqueados", http.StatusInternalServerError)
		return
	}

	w.Header().Set("Content-Type", "text/html")
	w.Write([]byte(response))
}

func main() {
	http.HandleFunc("/scrape", scrapeHandler)
	fmt.Println("Servidor escuchando en http://localhost:8080")
	http.ListenAndServe(":8081", nil)
}
