const fs=require('fs');
const h=fs.readFileSync('D:/laragon/www/mdu/admin/index.html','utf8');
const m=h.match(/<script>([\s\S]*?)<\/script>/);
fs.writeFileSync('D:/laragon/www/mdu/admin_script.js',m[1]);
console.log('wrote '+m[1].length+' chars');
