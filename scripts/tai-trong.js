const urls = process.argv.slice(2);
if (!urls.length) { console.error('Usage: node scripts/tai-trong.js <url> [...]'); process.exit(1); }
const results = [];
for (const url of urls) {
  const start = performance.now();
  try { const r = await fetch(url); const ms = Math.round(performance.now()-start); results.push({url,status:r.status,ms}); } catch (e) { results.push({url,error:String(e)}); }
}
console.table(results);
