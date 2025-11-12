/**
 * Test script to verify Turbopack timeout fix
 * This script checks if the configuration changes have been applied correctly
 */

const fs = require('fs');
const path = require('path');

// Function to check if a file contains specific content
function fileContains(filePath, searchString) {
  try {
    const content = fs.readFileSync(filePath, 'utf8');
    return content.includes(searchString);
  } catch (error) {
    console.error(`Error reading file ${filePath}:`, error.message);
    return false;
  }
}

// Function to check if a file exists
function fileExists(filePath) {
  return fs.existsSync(filePath);
}

console.log('🔍 Testing Turbopack Timeout Fix...\n');

// Check 1: next.config.mjs should contain turbopack configuration
const nextConfigPath = path.join(__dirname, 'v0-pertamina-frontend-build', 'next.config.mjs');
console.log('1. Checking next.config.mjs...');
if (fileContains(nextConfigPath, 'maxMemoryCacheSize: 512 * 1024 * 1024') && 
    fileContains(nextConfigPath, 'maxConcurrentBuilds: 2') &&
    fileContains(nextConfigPath, 'timeout: 30000')) {
  console.log('   ✅ Turbopack configuration found');
} else {
  console.log('   ❌ Turbopack configuration missing or incorrect');
}

// Check 2: package.json should use --turbo flag and port 3000
const packageJsonPath = path.join(__dirname, 'v0-pertamina-frontend-build', 'package.json');
console.log('2. Checking package.json...');
if (fileContains(packageJsonPath, '"dev": "next dev --turbo -p 3000"')) {
  console.log('   ✅ Correct dev script found');
} else {
  console.log('   ❌ Incorrect dev script');
}

// Check 3: start-fullstack.js should reference port 3000
const startScriptPath = path.join(__dirname, 'start-fullstack.js');
console.log('3. Checking start-fullstack.js...');
if (fileContains(startScriptPath, 'Next.js frontend (Next.js dev server) on port 3000')) {
  console.log('   ✅ Port 3000 reference found');
} else {
  console.log('   ❌ Port 3000 reference missing');
}

// Check 4: TURBOPACK_TIMEOUT_FIX.md should exist
const fixDocPath = path.join(__dirname, 'TURBOPACK_TIMEOUT_FIX.md');
console.log('4. Checking TURBOPACK_TIMEOUT_FIX.md...');
if (fileExists(fixDocPath)) {
  console.log('   ✅ TURBOPACK_TIMEOUT_FIX.md exists');
} else {
  console.log('   ❌ TURBOPACK_TIMEOUT_FIX.md missing');
}

// Check 5: README.md should reference the fix
const readmePath = path.join(__dirname, 'README.md');
console.log('5. Checking README.md...');
if (fileContains(readmePath, '[TURBOPACK_TIMEOUT_FIX.md](TURBOPACK_TIMEOUT_FIX.md)')) {
  console.log('   ✅ README.md references the fix documentation');
} else {
  console.log('   ❌ README.md does not reference the fix documentation');
}

console.log('\n✅ Turbopack Timeout Fix verification complete!');
console.log('If all checks passed, the timeout issue should be resolved.');
console.log('If any checks failed, please review the configuration files.');