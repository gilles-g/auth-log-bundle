#!/usr/bin/env node
import { spawn } from 'child_process';
import { resolve } from 'path';

const cliPath = resolve(process.cwd(), 'dist/index.js');

console.log('Testing SpiriitAuthLogBundle CLI...\n');

const cli = spawn('node', [cliPath], {
  cwd: '/tmp/test-cli',
  stdio: ['pipe', 'pipe', 'pipe']
});

let output = '';

cli.stdout.on('data', (data) => {
  output += data.toString();
  process.stdout.write(data);
});

cli.stderr.on('data', (data) => {
  process.stderr.write(data);
});

// Simulate user inputs
setTimeout(() => cli.stdin.write('\n'), 500);  // Welcome screen
setTimeout(() => cli.stdin.write('security@example.com\n'), 1500);  // Email
setTimeout(() => cli.stdin.write('Security Team\n'), 2500);  // Name
setTimeout(() => {
  cli.stdin.write('\x1B[B');  // Arrow down
  setTimeout(() => cli.stdin.write('\n'), 200);  // Select "No" for messenger
}, 3500);
setTimeout(() => {
  cli.stdin.write('\x1B[B');  // Arrow down to ipApi
  setTimeout(() => cli.stdin.write('\n'), 200);  // Select ipApi
}, 4500);

setTimeout(() => {
  cli.kill();
  console.log('\n\n=== Test completed ===');
  process.exit(0);
}, 6000);
