const fs = require('fs');
const files = ['quran.html', 'js/quran.js', 'css/style.css'];
let report = [];
for (const f of files) {
  if (!fs.existsSync(f)) { report.push(f + ': MISSING'); continue; }
  const s = fs.readFileSync(f, 'utf8');            // baca UTF-8
  const fixed = Buffer.from(s, 'latin1').toString('utf8'); // round-trip fix
  fs.writeFileSync(f, fixed, 'utf8');              // tulis kembali UTF-8 (no BOM)
  const chk = fs.readFileSync(f, 'utf8');
  const garbled = chk.includes('Ã') || chk.includes('Ø¨') || chk.includes('â€');
  report.push(f + ': ' + (garbled ? 'STILL-GARBLED' : 'FIXED'));
}
fs.writeFileSync('_fixenc_report.txt', report.join('\n') + '\n');
console.log(report.join('\n'));
