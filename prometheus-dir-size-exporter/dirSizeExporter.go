package main

import (
	"fmt"
	"io/ioutil"
	"log"
	"net/http"
	"os"
	"path/filepath"
	"time"

	"github.com/prometheus/client_golang/prometheus"
	"github.com/prometheus/client_golang/prometheus/promhttp"
	"github.com/pschou/go-params"
)

var version = "1.0"
var scanDir string = "./"
var maxFolderSizeBytes int64 = 10737418240 // 10Gb
var scanTime int = 300                     // 5min
var exporterPort string = "9628"

// Объявление метрик
var (
	// Метрика размер каталога
	fldSizeMetric = prometheus.NewGaugeVec(
		prometheus.GaugeOpts{
			Name: "dir_size_bytes",
			Help: "",
		},
		[]string{"folder_name"},
	)
	// Метрика: время сканирования каталога
	fldScanTime = prometheus.NewGauge(
		prometheus.GaugeOpts{
			Name: "dir_scan_time_sec",
			Help: "",
		},
	)
)

// Инициализация метрики
func init() {
	prometheus.MustRegister(fldSizeMetric)
	prometheus.MustRegister(fldScanTime)
}

func main() {

	// Определение входящих параметров
	params.Usage = func() {
		fmt.Fprintf(params.CommandLine.Output(), "Prometheus folder size exporter. (Version: %s)\n\nUsage: %s: [options...]\n\n", version, os.Args[0])
		params.PrintDefaults()
	}

	params.GroupingSet("Options")

	params.StringVar(&scanDir, "scanDir", scanDir, "Path for scanning", "DIR")
	params.Int64Var(&maxFolderSizeBytes, "maxFolderSizeBytes", maxFolderSizeBytes, "Max folder size in bytes for show", "BYTES")
	params.IntVar(&scanTime, "scanTime", scanTime, "Period seconds for folder scan", "SECONDS")
	params.StringVar(&exporterPort, "exporterPort", exporterPort, "Port for run exporter", "PORT")
	params.Parse()

	fmt.Println("Scanning folder:", scanDir)

	// Запуск вебсервера и процесса сканирования каталога
	runMetricsServer(scanDir, maxFolderSizeBytes, scanTime, exporterPort)

}

func runMetricsServer(scanDir string, maxFolderSizeBytes int64, scanTime int, exporterPort string) {

	// Запуск в отдельном процессе сканирование каталога
	go func() {
		var m = make(map[string]int64)
		var count int64 = time.Now().Unix()
		for {
			timeStartNow := time.Now().Unix()
			files, err := ioutil.ReadDir(scanDir)
			count++

			if err != nil {
				log.Fatal(err)
			}

			for _, f := range files {
				if f.IsDir() {
					var currentFolderSize int64 = int64(DirSize(scanDir + "/" + f.Name()))
					if currentFolderSize > maxFolderSizeBytes {
						fldSizeMetric.With(prometheus.Labels{"folder_name": f.Name()}).Set(float64(currentFolderSize))
						// Сохраняем список каталогов для последующей очистки
						m[f.Name()] = count
					}
				}
			}
			// Подсчет времени сканирования каталога
			fldScanTime.Set(float64(time.Now().Unix() - timeStartNow))
			// Почистим старые значения метрик
			for key, value := range m {
				if value < count {
					delete(m, key)
					fldSizeMetric.DeleteLabelValues(key)
				}
			}
			// Спим
			time.Sleep(time.Duration(scanTime) * time.Second)
		}
	}()

	// Запуск вебсервера
	http.Handle("/metrics", promhttp.Handler())
	log.Fatal(http.ListenAndServe(":"+exporterPort, nil))

}

// Функция подсчета размера каталога
func DirSize(path string) int64 {
	var size int64
	filepath.Walk(path, func(_ string, info os.FileInfo, err error) error {
		if err != nil {
			return err
		}
		if !info.IsDir() {
			size += info.Size()
		}
		return err
	})
	return size
}
