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
## Сборка
go mod init prometheus-dir-size-exporter
go get github.com/pschou/go-params
go get github.com/prometheus/client_golang/prometheus
go get github.com/prometheus/client_golang/prometheus/promauto
go get github.com/prometheus/client_golang/prometheus/promhttp
go build -o ./prometheus_dir_size_exporter ./dirSizeExporter.go
## Build for Linux
GOOS=linux GOARCH=amd64 go build -o ./prometheus_dir_size_exporter ./dirSizeExporter.go
```

```
## Создание сервиса
vim /usr/lib/systemd/system/folder_size_exporter.service

[Unit]
Description=Prometheus Folder size Exporter
Wants=network-online.target
After=network-online.target

[Service]
User=root
Group=root
Type=simple
ExecStart=/usr/local/bin/prometheus_dir_size_exporter --scanDir=/nfs

[Install]
WantedBy=multi-user.target

ln -s /usr/lib/systemd/system/folder_size_exporter.service /etc/systemd/system/multi-user.target.wants/folder_size_exporter.service
systemctl daemon-reload
systemctl start folder_size_exporter
systemctl enable folder_size_exporter

```

```
## Настройка prometheus
vim /etc/prometheus/prometheus.yml
  - job_name: "folder_size_exporter"
    scrape_interval: 10s
    static_configs:
      - targets: ["localhost:9628"]

systemctl restart prometheus
```