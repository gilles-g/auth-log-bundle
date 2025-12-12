# SpiriitAuthLogBundle Configuration CLI

Interactive CLI tool built with [ink](https://github.com/vadimdemedes/ink) to configure your `spiriit_auth_log.yaml` file.

## Installation

```bash
cd cli
npm install
```

## Usage

### Run the interactive configurator

```bash
npm start
```

Or directly:

```bash
node index.js
```

### What it does

The CLI will guide you through configuring:

1. **Email Settings**: Sender email and name for security notifications
2. **Messenger Integration**: Enable/disable Symfony Messenger for async processing
3. **Geolocation Provider**: Choose between no geolocation, ipApi, or GeoIP2
4. **GeoIP2 Path**: If using GeoIP2, specify the database path

The tool will generate a `config/packages/spiriit_auth_log.yaml` file with your configuration.

## Example

```
🔐 SpiriitAuthLogBundle Configuration

This interactive tool will help you configure your spiriit_auth_log.yaml file.

📧 Email Configuration
Enter the sender email address:
➤ no-reply@example.com

👤 Sender Name
Enter the sender name:
➤ Security Team

📨 Messenger Integration
Enable Symfony Messenger for async processing?
  ○ Yes - Enable Symfony Messenger integration
  ● No - Process synchronously

🌍 Location Provider
Select a geolocation provider:
  ● None - No geolocation
  ○ ipApi - Use IP API (free tier available)
  ○ geoip2 - Use GeoIP2 (requires database)

✅ Configuration Complete!
```

## Requirements

- Node.js 18.x or higher
- npm 9.x or higher
