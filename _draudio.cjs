const fs = require('fs');
const f = 'js/quran.js';
let s = fs.readFileSync(f, 'utf8');
const has = s.includes('quran-audio');
s = s.replace(/^[ \t]*<audio class="quran-audio"[^\n]*\n?/gm, '');
fs.writeFileSync(f, s, 'utf8');
console.log('had:' + has, 'nowContains:' + s.includes('quran-audio'));
