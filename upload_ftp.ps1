# FTP Upload Script untuk InfinityFree
# Upload semua file project parkiran_restoran ke hosting

$ftpHost = "ftpupload.net"
$ftpUser = "if0_41607120"
$ftpPass = "GALUHGANTENG15"
$localPath = "c:\xampp\htdocs\parkiran_restoran"
$remotePath = "/htdocs"

# Daftar folder/file yang TIDAK perlu diupload
$excludeDirs = @(".git", "node_modules", ".vscode")
$excludeFiles = @("upload_ftp.ps1")

function Upload-FTPFile {
    param(
        [string]$LocalFile,
        [string]$RemoteFile
    )
    
    try {
        $ftpUri = "ftp://${ftpHost}${RemoteFile}"
        $request = [System.Net.FtpWebRequest]::Create($ftpUri)
        $request.Method = [System.Net.WebRequestMethods+Ftp]::UploadFile
        $request.Credentials = New-Object System.Net.NetworkCredential($ftpUser, $ftpPass)
        $request.UseBinary = $true
        $request.UsePassive = $true
        $request.KeepAlive = $false
        $request.EnableSsl = $false
        
        $fileContent = [System.IO.File]::ReadAllBytes($LocalFile)
        $request.ContentLength = $fileContent.Length
        
        $requestStream = $request.GetRequestStream()
        $requestStream.Write($fileContent, 0, $fileContent.Length)
        $requestStream.Close()
        
        $response = $request.GetResponse()
        $response.Close()
        
        Write-Host "[OK] $RemoteFile" -ForegroundColor Green
        return $true
    }
    catch {
        Write-Host "[FAIL] $RemoteFile - $($_.Exception.Message)" -ForegroundColor Red
        return $false
    }
}

function Create-FTPDirectory {
    param(
        [string]$RemoteDir
    )
    
    try {
        $ftpUri = "ftp://${ftpHost}${RemoteDir}"
        $request = [System.Net.FtpWebRequest]::Create($ftpUri)
        $request.Method = [System.Net.WebRequestMethods+Ftp]::MakeDirectory
        $request.Credentials = New-Object System.Net.NetworkCredential($ftpUser, $ftpPass)
        $request.UsePassive = $true
        $request.KeepAlive = $false
        
        $response = $request.GetResponse()
        $response.Close()
        
        Write-Host "[DIR] $RemoteDir created" -ForegroundColor Cyan
    }
    catch {
        # Directory mungkin sudah ada, abaikan error
    }
}

# Kumpulkan semua file
Write-Host "========================================" -ForegroundColor Yellow
Write-Host " Uploading to InfinityFree via FTP" -ForegroundColor Yellow
Write-Host "========================================" -ForegroundColor Yellow
Write-Host ""

$allFiles = Get-ChildItem -Path $localPath -Recurse -File | Where-Object {
    $relativePath = $_.FullName.Substring($localPath.Length)
    $skip = $false
    foreach ($dir in $excludeDirs) {
        if ($relativePath -like "*\$dir\*" -or $relativePath -like "*\$dir") {
            $skip = $true
            break
        }
    }
    foreach ($file in $excludeFiles) {
        if ($_.Name -eq $file) {
            $skip = $true
            break
        }
    }
    -not $skip
}

Write-Host "Total files to upload: $($allFiles.Count)" -ForegroundColor Yellow
Write-Host ""

# Kumpulkan semua direktori yang diperlukan
$dirs = @()
foreach ($file in $allFiles) {
    $relativePath = $file.FullName.Substring($localPath.Length)
    $remoteFilePath = $remotePath + $relativePath.Replace("\", "/")
    $remoteDir = [System.IO.Path]::GetDirectoryName($remoteFilePath).Replace("\", "/")
    
    if ($remoteDir -and $remoteDir -ne $remotePath -and $dirs -notcontains $remoteDir) {
        $dirs += $remoteDir
    }
}

# Buat direktori (sort supaya parent dulu)
$dirs = $dirs | Sort-Object
Write-Host "Creating $($dirs.Count) directories..." -ForegroundColor Cyan
foreach ($dir in $dirs) {
    # Buat setiap level parent directory
    $parts = $dir.Split("/") | Where-Object { $_ -ne "" }
    $current = ""
    foreach ($part in $parts) {
        $current += "/$part"
        Create-FTPDirectory -RemoteDir $current
    }
}

Write-Host ""
Write-Host "Uploading files..." -ForegroundColor Yellow

$success = 0
$fail = 0

foreach ($file in $allFiles) {
    $relativePath = $file.FullName.Substring($localPath.Length)
    $remoteFilePath = $remotePath + $relativePath.Replace("\", "/")
    
    $result = Upload-FTPFile -LocalFile $file.FullName -RemoteFile $remoteFilePath
    if ($result) { $success++ } else { $fail++ }
}

Write-Host ""
Write-Host "========================================" -ForegroundColor Yellow
Write-Host " Upload Complete!" -ForegroundColor Green
Write-Host " Success: $success | Failed: $fail" -ForegroundColor Yellow
Write-Host "========================================" -ForegroundColor Yellow
Write-Host ""
Write-Host "Website URL: http://galuhparkir.wuaze.com" -ForegroundColor Cyan
Write-Host "Setup URL: http://galuhparkir.wuaze.com/setup.php" -ForegroundColor Cyan
