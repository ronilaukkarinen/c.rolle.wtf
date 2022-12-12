### Ruuvi raw data

WIP.

Shows RAW ruuvi data from InfluxDB.

#### Installation

1. Install [RuuviBridge](https://github.com/Scrin/RuuviBridge)
2. Run `composer install`

#### Systemd service

/etc/systemd/system/ruuvibridge.service:

```ini
[Unit]
Description=RuuviBridge

[Service]
Type=simple
WorkingDirectory=/home/rolle/RuuviBridge
Type=simple
ExecStart=/home/rolle/RuuviBridge/ruuvibridge
Restart=on-failure
RestartSec=3
User=root

[Install]
WantedBy=multi-user.target
```
