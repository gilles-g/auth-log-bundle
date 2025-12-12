import React, { useState } from 'react';
import { render, Box, Text } from 'ink';
import TextInput from 'ink-text-input';
import SelectInput from 'ink-select-input';
import { writeFileSync } from 'fs';
import { stringify } from 'yaml';
import { resolve } from 'path';

const ConfigureApp = () => {
	const [step, setStep] = useState(0);
	const [config, setConfig] = useState({
		senderEmail: '',
		senderName: '',
		messengerEnabled: false,
		locationProvider: 'none',
		geoip2Path: ''
	});

	const updateConfig = (key, value) => {
		setConfig(prev => ({ ...prev, [key]: value }));
	};

	const nextStep = () => setStep(prev => prev + 1);

	const generateYaml = () => {
		const yamlConfig = {
			spiriit_auth_log: {
				transports: {
					sender_email: config.senderEmail,
					sender_name: config.senderName
				}
			}
		};

		if (config.messengerEnabled) {
			yamlConfig.spiriit_auth_log.messenger = 'messenger.default_bus';
		}

		if (config.locationProvider !== 'none') {
			yamlConfig.spiriit_auth_log.location = {
				provider: config.locationProvider
			};

			if (config.locationProvider === 'geoip2' && config.geoip2Path) {
				yamlConfig.spiriit_auth_log.location.geoip2_database_path = config.geoip2Path;
			}
		}

		return stringify(yamlConfig);
	};

	const saveConfig = () => {
		const yaml = generateYaml();
		const outputPath = resolve(process.cwd(), 'config/packages/spiriit_auth_log.yaml');
		
		try {
			writeFileSync(outputPath, yaml, 'utf8');
			return outputPath;
		} catch (error) {
			return null;
		}
	};

	// Step 0: Welcome
	if (step === 0) {
		return (
			<Box flexDirection="column" padding={1}>
				<Box marginBottom={1}>
					<Text bold color="cyan">
						🔐 SpiriitAuthLogBundle Configuration
					</Text>
				</Box>
				<Box marginBottom={1}>
					<Text>
						This interactive tool will help you configure your spiriit_auth_log.yaml file.
					</Text>
				</Box>
				<Box>
					<Text dimColor>Press Enter to continue...</Text>
				</Box>
				<TextInput
					value=""
					onChange={() => {}}
					onSubmit={nextStep}
				/>
			</Box>
		);
	}

	// Step 1: Sender Email
	if (step === 1) {
		return (
			<Box flexDirection="column" padding={1}>
				<Box marginBottom={1}>
					<Text bold color="yellow">📧 Email Configuration</Text>
				</Box>
				<Box marginBottom={1}>
					<Text>Enter the sender email address:</Text>
				</Box>
				<Box>
					<Text color="green">➤ </Text>
					<TextInput
						value={config.senderEmail}
						onChange={(value) => updateConfig('senderEmail', value)}
						onSubmit={nextStep}
						placeholder="no-reply@yourdomain.com"
					/>
				</Box>
			</Box>
		);
	}

	// Step 2: Sender Name
	if (step === 2) {
		return (
			<Box flexDirection="column" padding={1}>
				<Box marginBottom={1}>
					<Text bold color="yellow">👤 Sender Name</Text>
				</Box>
				<Box marginBottom={1}>
					<Text>Enter the sender name:</Text>
				</Box>
				<Box>
					<Text color="green">➤ </Text>
					<TextInput
						value={config.senderName}
						onChange={(value) => updateConfig('senderName', value)}
						onSubmit={nextStep}
						placeholder="Your App Security"
					/>
				</Box>
			</Box>
		);
	}

	// Step 3: Messenger Integration
	if (step === 3) {
		const items = [
			{ label: 'Yes - Enable Symfony Messenger integration', value: true },
			{ label: 'No - Process synchronously', value: false }
		];

		return (
			<Box flexDirection="column" padding={1}>
				<Box marginBottom={1}>
					<Text bold color="magenta">📨 Messenger Integration</Text>
				</Box>
				<Box marginBottom={1}>
					<Text>Enable Symfony Messenger for async processing?</Text>
				</Box>
				<SelectInput
					items={items}
					onSelect={(item) => {
						updateConfig('messengerEnabled', item.value);
						nextStep();
					}}
				/>
			</Box>
		);
	}

	// Step 4: Location Provider
	if (step === 4) {
		const items = [
			{ label: 'None - No geolocation', value: 'none' },
			{ label: 'ipApi - Use IP API (free tier available)', value: 'ipApi' },
			{ label: 'geoip2 - Use GeoIP2 (requires database)', value: 'geoip2' }
		];

		return (
			<Box flexDirection="column" padding={1}>
				<Box marginBottom={1}>
					<Text bold color="blue">🌍 Location Provider</Text>
				</Box>
				<Box marginBottom={1}>
					<Text>Select a geolocation provider:</Text>
				</Box>
				<SelectInput
					items={items}
					onSelect={(item) => {
						updateConfig('locationProvider', item.value);
						if (item.value === 'geoip2') {
							nextStep();
						} else {
							setStep(5); // Skip GeoIP2 path step
						}
					}}
				/>
			</Box>
		);
	}

	// Step 5: GeoIP2 Database Path (only if geoip2 selected)
	if (step === 5 && config.locationProvider === 'geoip2') {
		return (
			<Box flexDirection="column" padding={1}>
				<Box marginBottom={1}>
					<Text bold color="blue">📁 GeoIP2 Database Path</Text>
				</Box>
				<Box marginBottom={1}>
					<Text>Enter the path to your GeoLite2-City.mmdb file:</Text>
				</Box>
				<Box>
					<Text color="green">➤ </Text>
					<TextInput
						value={config.geoip2Path}
						onChange={(value) => updateConfig('geoip2Path', value)}
						onSubmit={() => setStep(6)}
						placeholder="%kernel.project_dir%/var/GeoLite2-City.mmdb"
					/>
				</Box>
			</Box>
		);
	}

	// Final Step: Summary and Save
	const yaml = generateYaml();
	const savedPath = step === 6 ? saveConfig() : null;

	return (
		<Box flexDirection="column" padding={1}>
			<Box marginBottom={1}>
				<Text bold color="green">✅ Configuration Complete!</Text>
			</Box>
			<Box marginBottom={1}>
				<Text bold>Generated YAML:</Text>
			</Box>
			<Box marginBottom={1} paddingLeft={2}>
				<Text>{yaml}</Text>
			</Box>
			{savedPath ? (
				<Box marginBottom={1}>
					<Text color="green">
						✓ Configuration saved to: {savedPath}
					</Text>
				</Box>
			) : (
				<Box marginBottom={1}>
					<Text color="red">
						✗ Could not save configuration. Please create the file manually.
					</Text>
				</Box>
			)}
			<Box marginTop={1}>
				<Text dimColor>Press Ctrl+C to exit</Text>
			</Box>
		</Box>
	);
};

render(<ConfigureApp />);
