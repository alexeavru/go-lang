## prometheus-dir-size-exporter
---
```
Prometheus folder size exporter. (Version: 1.0)

Usage: ./prometheus_dir_size_exporter: [options...]

Options options:
  --exporterPort PORT   Port for run exporter  (Default: "9628")
  --maxFolderSizeBytes BYTES  Max folder size in bytes for show  (Default: 10737418240)
  --scanDir DIR         Path for scanning  (Default: "./")
  --scanTime SECONDS    Period seconds for folder scan  (Default: 300)
```
```
go mod init prometheus-dir-size-exporter
go get github.com/pschou/go-params
go get github.com/prometheus/client_golang/prometheus
go get github.com/prometheus/client_golang/prometheus/promauto
go get github.com/prometheus/client_golang/prometheus/promhttp
go build -o ./prometheus_dir_size_exporter ./dirSizeExporter.go
## Build for Linux
GOOS=linux GOARCH=amd64 go build -o ./prometheus_dir_size_exporter ./dirSizeExporter.go
```