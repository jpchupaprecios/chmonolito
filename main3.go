package main

import (
	"compress/gzip"
	"flag"
	"fmt"
	"io"
	"net/http"
	"net/url"
	"os"
	"strings"
	"time"
)

// decodeChapiDirectCall verifica si la respuesta está bloqueada y la limpia
func decodeChapiDirectCall(response string) string {
	if strings.Contains(response, "item cannot be shipped") || strings.Contains(response, "To discuss automated") {
		return "unauthorized"
	}
	cleanResponse := strings.ReplaceAll(response, "\r", "")
	cleanResponse = strings.ReplaceAll(cleanResponse, "\n", "")
	cleanResponse = strings.ReplaceAll(cleanResponse, "&&&", ",")
	return cleanResponse
}

func main() {
	// Definición de flags para recibir parámetros desde la línea de comandos
	targetURL := flag.String("url", "", "URL destino a scrapear")
	cookie := flag.String("cookie", "", "Cookie a enviar en la solicitud")
	userAgent := flag.String("useragent", "", "User-Agent a enviar en la solicitud")
	// Parámetros del proxy (valores por defecto según tu ejemplo)
	proxyHost := flag.String("proxyHost", "dc.oxylabs.io", "Host del proxy")
	proxyPort := flag.String("proxyPort", "8000", "Puerto del proxy")
	proxyUser := flag.String("proxyUser", "user-chupaprecios_lDWEa-country-US", "Usuario del proxy")
	proxyPass := flag.String("proxyPass", "+Aq1w2e3r4t5", "Contraseña del proxy")

	flag.Parse()

	if *targetURL == "" {
		fmt.Println("Debes proporcionar la URL destino usando -url")
		os.Exit(1)
	}

	// Construir la URL del proxy con las credenciales
	proxyURLStr := fmt.Sprintf("http://%s:%s@%s:%s", *proxyUser, *proxyPass, *proxyHost, *proxyPort)
	proxyParsed, err := url.Parse(proxyURLStr)
	if err != nil {
		fmt.Printf("Error parseando el proxy: %v\n", err)
		os.Exit(1)
	}

	// Configurar el transporte HTTP para usar el proxy
	transport := &http.Transport{
		Proxy: http.ProxyURL(proxyParsed),
	}

	// Crear un cliente HTTP con timeout
	client := &http.Client{
		Transport: transport,
		Timeout:   15 * time.Second,
	}

	// Crear la solicitud HTTP
	req, err := http.NewRequest("GET", *targetURL, nil)
	if err != nil {
		fmt.Printf("Error creando la solicitud: %v\n", err)
		os.Exit(1)
	}

	// Agregar los headers
	req.Header.Set("Connection", "keep-alive")
	req.Header.Set("Accept", "*/*")
	req.Header.Set("Content-Language", "es-US")
	if *userAgent != "" {
		req.Header.Set("User-Agent", *userAgent)
	}
	if *cookie != "" {
		req.Header.Set("Cookie", *cookie)
	}

	// Realizar la solicitud
	resp, err := client.Do(req)
	if err != nil {
		fmt.Printf("Error realizando la solicitud: %v\n", err)
		os.Exit(1)
	}
	defer resp.Body.Close()

	var body []byte
	// Si la respuesta está comprimida con gzip, descomprimirla
	if strings.Contains(resp.Header.Get("Content-Encoding"), "gzip") {
		reader, err := gzip.NewReader(resp.Body)
		if err != nil {
			fmt.Printf("Error creando lector gzip: %v\n", err)
			os.Exit(1)
		}
		defer reader.Close()
		body, err = io.ReadAll(reader)
		if err != nil {
			fmt.Printf("Error leyendo respuesta descomprimida: %v\n", err)
			os.Exit(1)
		}
	} else {
		body, err = io.ReadAll(resp.Body)
		if err != nil {
			fmt.Printf("Error leyendo respuesta: %v\n", err)
			os.Exit(1)
		}
	}

	responseStr := decodeChapiDirectCall(string(body))
	if responseStr == "unauthorized" {
		fmt.Println("Respuesta bloqueada")
		os.Exit(1)
	}

	// Imprimir el HTML obtenido en la consola
	fmt.Println(responseStr)
}
