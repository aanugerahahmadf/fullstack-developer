const { spawn, execSync } = require('child_process');
const path = require('path');
const fs = require('fs');

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

// Function to copy directory recursively
function copyDir(src, dest) {
  fs.mkdirSync(dest, { recursive: true });
  const entries = fs.readdirSync(src, { withFileTypes: true });
  
  for (const entry of entries) {
    const srcPath = path.join(src, entry.name);
    const destPath = path.join(dest, entry.name);
    
    if (entry.isDirectory()) {
      copyDir(srcPath, destPath);
    } else {
      fs.copyFileSync(srcPath, destPath);
    }
  }
}

// Main build and deployment process
async function buildAndDeploy() {
  try {
    // 1. Build Next.js frontend
    console.log('📦 Building Next.js frontend...');
    await executeCommand(
      'npm run build', 
      path.join(__dirname, 'v0-pertamina-frontend-build'), 
      'Next.js Build'
    );
    
    // 2. Copy public assets to standalone directory if needed
    const standaloneDir = path.join(__dirname, 'v0-pertamina-frontend-build', '.next', 'standalone');
    const publicDir = path.join(__dirname, 'v0-pertamina-frontend-build', 'public');
    const standalonePublicDir = path.join(standaloneDir, 'public');
    
    if (fs.existsSync(publicDir) && fs.existsSync(standaloneDir)) {
      console.log('📂 Copying public assets to standalone directory...');
      copyDir(publicDir, standalonePublicDir);
      console.log('✅ Public assets copied successfully\n');
    }
    
    // 3. Install backend dependencies for production
    console.log('📦 Installing backend dependencies...');
    await executeCommand(
      'composer install --no-dev --optimize-autoloader', 
      path.join(__dirname, 'backend-new'), 
      'Backend Dependencies Installation'
    );
    
    // 4. Optimize Laravel for production
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
    
    // 5. Generate deployment information
    const deployInfo = {
      timestamp: new Date().toISOString(),
      version: '1.0.0',
      buildPath: path.join(__dirname, 'deploy'),
      components: {
        frontend: {
          path: path.join(__dirname, 'v0-pertamina-frontend-build', '.next'),
          type: 'Next.js'
        },
        backend: {
          path: path.join(__dirname, 'backend-new'),
          type: 'Laravel'
        }
      }
    };
    
    // Save deployment information
    const deployDir = path.join(__dirname, 'deploy');
    fs.mkdirSync(deployDir, { recursive: true });
    fs.writeFileSync(
      path.join(deployDir, 'deploy-info.json'),
      JSON.stringify(deployInfo, null, 2)
    );
    
    console.log('✅ Deployment information saved\n');
    
    console.log('🎉 Build and deployment process completed successfully!');
    console.log('📁 Deployment artifacts are located in the "deploy" directory');
    console.log('📝 Next steps:');
    console.log('   1. Upload the contents of the deploy directory to your server');
    console.log('   2. Configure your web server to serve the Laravel backend');
    console.log('   3. Ensure the Next.js standalone server is running');
    console.log('   4. Set up your environment variables on the production server');
    
  } catch (error) {
    console.error('💥 Build and deployment process failed:', error.message);
    process.exit(1);
  }
}

// Run the build and deployment process
buildAndDeploy();