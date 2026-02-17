# Load environment variables from the .env file
$envContent = Get-Content .env -Raw | ConvertFrom-StringData

# Step 1: Run database migrations and seeders
Write-Host "==> Ejecutando migraciones y seeders..."
php artisan migrate --seed

# Step 2: Read and process the SQL file for role creation
Write-Host "==> Creando roles..."
$sqlContent = Get-Content "database/sql/roles.sql" -Raw

# Replace placeholders with values from the .env file
$sqlContent = $sqlContent -replace "{{DB_USER_PASS}}", $envContent.DB_USER_PASS
$sqlContent = $sqlContent -replace "{{DB_DATABASE}}", $envContent.DB_DATABASE
$sqlContent = $sqlContent -replace "{{DB_STUDENT_PASS}}", $envContent.DB_STUDENT_PASS
$sqlContent = $sqlContent -replace "{{DB_PROFESSOR_PASS}}", $envContent.DB_PROFESSOR_PASS
$sqlContent = $sqlContent -replace "{{DB_RESEARCH_PASS}}", $envContent.DB_RESEARCH_PASS

# Save the processed SQL to a temporary file
$tempFile = "temp_roles.sql"
$sqlContent | Out-File $tempFile -Encoding UTF8

# Execute the SQL script using MySQL
$mysqlPath = "C:\xampp\mysql\bin\mysql.exe"
$mysqlArgs = "-u$($envContent.DB_USERNAME) -p$($envContent.DB_PASSWORD) -h$($envContent.DB_HOST) -P$($envContent.DB_PORT) $($envContent.DB_DATABASE) -e `"source $tempFile`""

Write-Host "Ejecutando: $mysqlPath $mysqlArgs"
Start-Process -FilePath $mysqlPath -ArgumentList $mysqlArgs -Wait -NoNewWindow

# Clean up temporary file
Remove-Item $tempFile -ErrorAction SilentlyContinue

Write-Host "==> Base de datos inicializada correctamente 🎉"