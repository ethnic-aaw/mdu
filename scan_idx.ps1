curl.exe -s 'http://localhost/mdu/index.html' | Select-String -Pattern 'Store.load|renderFromData|DOMContentLoaded|initApp|\.then' -AllMatches | Out-String
