# CLI Troubleshooting Guide

## Common Issues and Solutions

### Issue: `make start` fails with "No rule to make target 'start'"

**Solution**: Make sure you're in the repository root directory, not the `cli/` directory.

```bash
cd /path/to/auth-log-bundle
make start
```

### Issue: Module not found errors

**Error**: `Error [ERR_MODULE_NOT_FOUND]: Cannot find package...`

**Solution**: Dependencies need to be installed. Run:

```bash
make install
# or
cd cli && npm install
```

### Issue: Build errors

**Solution**: Clean and rebuild:

```bash
make clean
make install
make build
```

### Issue: Cannot find dist/index.js

**Solution**: The CLI needs to be built before running:

```bash
make build
# or
cd cli && npm run build
```

## Development Workflow

1. **First time setup**:
   ```bash
   make install
   ```

2. **After making changes to index.js**:
   ```bash
   make build
   ```

3. **Running the CLI**:
   ```bash
   make start
   ```

## CI/CD

The repository includes automated testing via GitHub Actions:

- `.github/workflows/cli-test.yml` - Tests the CLI build on Node 18, 20, and 22
- Tests run automatically on pull requests that modify CLI files

## Manual Testing

Test the build locally:

```bash
make cli-test
```

This will:
1. Install dependencies
2. Build the CLI
3. Verify the build output
4. Check for syntax errors

## Requirements

- Node.js 18.x or higher
- npm 9.x or higher
- Make (for using Makefile commands)

## Quick Reference

| Command | Description |
|---------|-------------|
| `make help` | Show available commands |
| `make install` | Install dependencies |
| `make build` | Build the CLI |
| `make start` | Run the CLI |
| `make clean` | Remove build artifacts |
| `make cli-test` | Test CLI build |
| `make test` | Run PHP tests |
