const { spawn } = require('child_process');
const path = require('path');
const fs = require('fs');
const os = require('os');

console.log('🚀 Starting Build and Deployment Process...');
console.log('==========================================\n');

// Function to execute a command and wait for completion
function executeCommand(command, cwd, name) {
  return new Promise((resolve, reject) => {
    console.log(`🔄 Executing ${name}: ${command}`);
    
    const process = spawn(command, { 
      cwd, 
      stdio: 'inherit', 
      shell: true
    });
    
    process.on('close', (code) => {
      if (code === 0) {
        console.log(`✅ ${name} completed successfully\n`);
        resolve();
      } else {
        console.error(`❌ ${name} failed with code ${code}\n`);
        reject(new Error(`${name} failed`));
      }
    });
    
    process.on('error', (err) => {
      console.error(`[ERROR] Failed to execute ${name}:`, err.message);
      reject(err);
    });
  });
}

// Function to clean previous build artifacts
function cleanPreviousBuild() {
  console.log('🧹 Cleaning previous build artifacts...');
  
  const standaloneDir = path.join(__dirname, 'v0-pertamina-frontend-build', '.next', 'standalone');
  
  // Remove standalone directory if it exists
  if (fs.existsSync(standaloneDir)) {
    fs.rmSync(standaloneDir, { recursive: true, force: true });
    console.log('✅ Previous standalone directory removed');
  }
  
  console.log('✅ Cleaning completed\n');
}

// Main build and deployment process
async function buildAndDeploy() {
  try {
    // 1. Clean previous build artifacts
    cleanPreviousBuild();
    
    // 2. Build Next.js frontend
    console.log('📦 Building Next.js frontend...');
    await executeCommand(
      'npm run build', 
      path.join(__dirname, 'v0-pertamina-frontend-build'), 
      'Next.js Build'
    );
    
    // 3. Copy public assets to standalone directory if needed
    const standaloneDir = path.join(__dirname, 'v0-pertamina-frontend-build', '.next', 'standalone');
    const publicDir = path.join(__dirname, 'v0-pertamina-frontend-build', 'public');
    const standalonePublicDir = path.join(standaloneDir, 'public');
    
    if (fs.existsSync(publicDir) && fs.existsSync(standaloneDir)) {
      console.log('📂 Copying public assets to standalone directory...');
      
      // Create public directory in standalone if it doesn't exist
      if (!fs.existsSync(standalonePublicDir)) {
        fs.mkdirSync(standalonePublicDir, { recursive: true });
      }
      
      // Copy all files from public to standalone public
      const files = fs.readdirSync(publicDir);
      files.forEach(file => {
        const src = path.join(publicDir, file);
        const dest = path.join(standalonePublicDir, file);
        fs.copyFileSync(src, dest);
        console.log(`   Copied ${file}`);
      });
      
      console.log('✅ Public assets copied successfully\n');
    }
    
    // 4. Install backend dependencies for production
    console.log('📦 Installing backend dependencies...');
    await executeCommand(
      'composer install --no-dev --optimize-autoloader', 
      path.join(__dirname, 'backend-new'), 
      'Backend Dependencies Installation'
    );
    
    // 5. Optimize Laravel for production
    console.log('⚙️  Optimizing Laravel for production...');
    await executeCommand(
      'php artisan config:cache', 
      path.join(__dirname, 'backend-new'), 
      'Laravel Config Cache'
    );
    
    await executeCommand(
      'php artisan route:cache', 
      path.join(__dirname, 'backend-new'), 
      'Laravel Route Cache'
    );
    
    await executeCommand(
      'php artisan view:cache', 
      path.join(__dirname, 'backend-new'), 
      'Laravel View Cache'
    );
    
    console.log('✅ Laravel optimization completed\n');
    
    // 6. Install streaming server dependencies
    console.log('📦 Installing streaming server dependencies...');
    await executeCommand(
      'npm install', 
      path.join(__dirname, 'streaming-server'), 
      'Streaming Server Dependencies Installation'
    );
    
    console.log('✅ Streaming server dependencies installed\n');
    
    console.log('🎉 Build and deployment process completed successfully!');
    console.log('📝 Next steps:');
    console.log('   1. The application is now built and ready to run');
    console.log('   2. Use start-fullstack.js to start all services');
    
  } catch (error) {
    console.error('💥 Build and deployment process failed:', error.message);
    process.exit(1);
  }
}

// Run the build and deployment process
buildAndDeploy();