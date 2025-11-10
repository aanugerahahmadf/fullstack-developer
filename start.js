const { spawn } = require('child_process');
const path = require('path');

console.log('🚀 Starting Pertamina Fullstack Application...');
console.log('==========================================\n');

// Function to start a process with optimized settings
function startProcess(command, args, cwd, name) {
  console.log(`🔄 Starting ${name}...`);
  const process = spawn(command, args, { 
    cwd, 
    stdio: 'pipe', 
    shell: true
  });
  
  process.stdout.on('data', (data) => {
    console.log(`[${name}] ${data.toString().trim()}`);
  });
  
  process.stderr.on('data', (data) => {
    console.log(`[${name} ERROR] ${data.toString().trim()}`);
  });
  
  process.on('close', (code) => {
    console.log(`[${name}] Process exited with code ${code}`);
  });
  
  process.on('error', (err) => {
    console.error(`[ERROR] Failed to start ${name}:`, err.message);
  });
  
  return process;
}

// Start Laravel backend (PHP server) on port 8000 with optimized settings
const laravel = startProcess(
  'php', 
  ['artisan', 'serve', '--port=8000', '--no-interaction', '--quiet'], 
  path.join(__dirname, 'backend-new'),
  'Laravel Backend'
);

// Start Next.js frontend in standalone mode on port 3000
const frontend = startProcess(
  'node', 
  ['server.js'], 
  path.join(__dirname, 'backend-new', 'frontend'),
  'Next.js Frontend'
);

// Handle process termination
process.on('SIGINT', () => {
  console.log('\n🛑 Shutting down servers...');
  laravel.kill();
  frontend.kill();
  process.exit(0);
});

console.log('\n✅ Servers starting up...');
console.log('📋 Access the application at: http://127.0.0.1:8000');
console.log('📁 Admin Panel: http://127.0.0.1:8000/admin');
console.log('📡 API Endpoints: http://127.0.0.1:8000/api/*');
console.log('\n🔄 Hot reloading enabled for instant updates');
console.log('🔄 Changes to React/Next.js code will be reflected immediately at http://127.0.0.1:8000');
console.log('\nPress Ctrl+C to stop both servers\n');