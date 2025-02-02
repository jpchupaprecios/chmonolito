package main

import (
	"fmt"
	"io/ioutil"
	"net/http"
	"net/url"
)

// Custom user agent.
const (
	userAgent = "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_6) " +
		"AppleWebKit/537.36 (KHTML, like Gecko) " +
		"Chrome/53.0.2785.143 " +
		"Safari/537.36"
)

// Datos del proxy.
const (
	proxyHost = "dc.oxylabs.io"
	proxyPort = "8000"
	proxyUser = "user-chupaprecios_lDWEa-country-US"
	proxyPass = "+Aq1w2e3r4t5"
)

// fetchUrl abre una URL con GET, utilizando un User-Agent personalizado y un proxy.
// Si la URL no se puede abrir, la envía por chFailedUrls.
// Si se abre correctamente, lee el cuerpo y lo imprime.
func fetchUrl(urlStr string, chFailedUrls chan string, chIsFinished chan bool) {
	// Construir la URL del proxy con las credenciales.
	proxyURLStr := fmt.Sprintf("http://%s:%s@%s:%s", proxyUser, proxyPass, proxyHost, proxyPort)
	proxyURL, err := url.Parse(proxyURLStr)
	if err != nil {
		chFailedUrls <- urlStr
		chIsFinished <- true
		return
	}

	// Crear un transporte HTTP que utilice el proxy.
	transport := &http.Transport{
		Proxy: http.ProxyURL(proxyURL),
	}

	// Crear un cliente HTTP con el transporte configurado.
	client := &http.Client{
		Transport: transport,
	}

	// Crear la solicitud HTTP.
	req, err := http.NewRequest("GET", urlStr, nil)
	if err != nil {
		chFailedUrls <- urlStr
		chIsFinished <- true
		return
	}

	// Agregar el User-Agent personalizado.
	req.Header.Set("User-Agent", userAgent)

	// Realizar la solicitud.
	resp, err := client.Do(req)
	if err != nil || resp.StatusCode != 200 {
		chFailedUrls <- urlStr
		chIsFinished <- true
		return
	}
	defer resp.Body.Close()

	// Leer el cuerpo de la respuesta.
	body, err := ioutil.ReadAll(resp.Body)
	if err != nil {
		chFailedUrls <- urlStr
		chIsFinished <- true
		return
	}

	// Imprimir el HTML obtenido.
	fmt.Printf("HTML de %s:\n%s\n", urlStr, body)

	// Notificar que se terminó.
	chIsFinished <- true
}

func main() {
	// Lista de URLs a abrir.
	urlsList := []string{
		"https://www.amazon.com/dp/B077ZJC1N9",
		// Puedes agregar más URLs aquí.
	}

	// Crear canales para rastrear URLs fallidas y finalización.
	chFailedUrls := make(chan string)
	chIsFinished := make(chan bool)

	// Lanzar cada solicitud en una goroutine.
	for _, urlStr := range urlsList {
		go fetchUrl(urlStr, chFailedUrls, chIsFinished)
	}

	// Recopilar las URLs fallidas.
	failedUrls := make([]string, 0)
	for i := 0; i < len(urlsList); {
		select {
		case url := <-chFailedUrls:
			failedUrls = append(failedUrls, url)
		case <-chIsFinished:
			i++
		}
	}

	// Imprimir las URLs que no se pudieron abrir.
	if len(failedUrls) > 0 {
		fmt.Println("No se pudieron obtener las siguientes URLs:", failedUrls)
	} else {
		fmt.Println("Todas las URLs se abrieron correctamente.")
	}
}
