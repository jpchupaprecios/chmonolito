package main

import (
	"compress/gzip"
	"fmt"
	"io"
	"net/http"
	"net/url"
	"strings"
	"time"
)

// Proxy representa la configuración de un proxy
type Proxy struct {
	Host string
	Port string
	User string
	Pass string
}

// decodeChapiDirectCall verifica si la respuesta está bloqueada y la limpia
func decodeChapiDirectCall(response string) string {
	// Verificar si la respuesta está bloqueada
	if strings.Contains(response, "item cannot be shipped") || strings.Contains(response, "To discuss automated") {
		return "unauthorized"
	}

	// Limpiar la respuesta
	cleanResponse := strings.ReplaceAll(response, "\r", "")
	cleanResponse = strings.ReplaceAll(cleanResponse, "\n", "")
	cleanResponse = strings.ReplaceAll(cleanResponse, "&&&", ",")
	return cleanResponse
}

// scrapeHandler maneja las solicitudes al endpoint /scrape
func scrapeHandler(w http.ResponseWriter, r *http.Request) {
	// Obtener la URL destino
	targetURL := r.URL.Query().Get("url")
	if targetURL == "" {
		http.Error(w, "Falta el parámetro 'url'", http.StatusBadRequest)
		return
	}

	// Obtener opcionalmente cookie y userAgent
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

	// Configuración del proxy único
	proxy := Proxy{
		Host: "dc.oxylabs.io",
		Port: "8000",
		User: "user-chupaprecios_lDWEa-country-US",
		Pass: "+Aq1w2e3r4t5",
	}

	// Configurar la URL del proxy
	proxyURL := fmt.Sprintf("http://%s:%s@%s:%s", proxy.User, proxy.Pass, proxy.Host, proxy.Port)
	proxyParsed, err := url.Parse(proxyURL)
	if err != nil {
		http.Error(w, fmt.Sprintf("Error parseando proxy: %v", err), http.StatusInternalServerError)
		return
	}

	// Crear un transporte que utilice el proxy
	transport := &http.Transport{
		Proxy: http.ProxyURL(proxyParsed),
	}

	// Crear el cliente HTTP con un timeout
	client := &http.Client{
		Transport: transport,
		Timeout:   15 * time.Second,
	}

	// Crear la solicitud HTTP
	req, err := http.NewRequest("GET", targetURL, nil)
	if err != nil {
		http.Error(w, fmt.Sprintf("Error creando la solicitud: %v", err), http.StatusInternalServerError)
		return
	}

	// Agregar headers personalizados
	for key, value := range headers {
		req.Header.Add(key, value)
	}

	// Realizar la solicitud a través del proxy
	resp, err := client.Do(req)
	if err != nil {
		http.Error(w, fmt.Sprintf("Error en la solicitud: %v", err), http.StatusInternalServerError)
		return
	}
	defer resp.Body.Close()

	// Leer la respuesta, descomprimiendo si es necesario
	var body []byte
	if strings.Contains(resp.Header.Get("Content-Encoding"), "gzip") {
		reader, err := gzip.NewReader(resp.Body)
		if err != nil {
			http.Error(w, fmt.Sprintf("Error descomprimiendo la respuesta: %v", err), http.StatusInternalServerError)
			return
		}
		defer reader.Close()
		body, err = io.ReadAll(reader)
		if err != nil {
			http.Error(w, fmt.Sprintf("Error leyendo la respuesta descomprimida: %v", err), http.StatusInternalServerError)
			return
		}
	} else {
		body, err = io.ReadAll(resp.Body)
		if err != nil {
			http.Error(w, fmt.Sprintf("Error leyendo la respuesta: %v", err), http.StatusInternalServerError)
			return
		}
	}

	// Verificar si la respuesta fue bloqueada
	responseStr := decodeChapiDirectCall(string(body))
	if responseStr == "unauthorized" {
		http.Error(w, "Respuesta bloqueada", http.StatusUnauthorized)
		return
	}

	// Devolver la respuesta
	w.Header().Set("Content-Type", "text/html")
	w.Write([]byte(responseStr))
}

func main() {
	http.HandleFunc("/scrape", scrapeHandler)
	fmt.Println("Servidor escuchando en http://localhost:8080")
	http.ListenAndServe(":8080", nil)
}
