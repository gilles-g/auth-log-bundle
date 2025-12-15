SHELL := /bin/bash

.PHONY: help install start build clean test

# Default target
help:
	@echo "SpiriitAuthLogBundle - Available commands:"
	@echo ""
	@echo "  make install    - Install CLI dependencies"
	@echo "  make build      - Build the CLI application"
	@echo "  make start      - Start the CLI configurator"
	@echo "  make clean      - Clean build artifacts"
	@echo "  make test       - Run PHP tests"
	@echo "  make cli-test   - Test CLI build"
	@echo ""

# Install CLI dependencies
install:
	@echo "Installing CLI dependencies..."
	cd cli && npm install

# Build the CLI
build:
	@echo "Building CLI..."
	cd cli && npm run build

# Start the CLI configurator (requires install and build)
start:
	@echo "Starting CLI configurator..."
	@if [ ! -d "cli/node_modules" ]; then \
		echo "Dependencies not installed. Running 'make install'..."; \
		$(MAKE) install; \
	fi
	@if [ ! -f "cli/dist/index.js" ]; then \
		echo "CLI not built. Running 'make build'..."; \
		$(MAKE) build; \
	fi
	cd cli && node dist/index.js

# Clean build artifacts
clean:
	@echo "Cleaning build artifacts..."
	rm -rf cli/dist
	rm -rf cli/node_modules

# Run PHP tests
test:
	composer test

# Test CLI build
cli-test:
	@echo "Testing CLI build..."
	cd cli && npm install && npm run build && node --check dist/index.js
	@echo "✓ CLI build successful"
