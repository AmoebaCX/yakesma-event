// Server statis minimal untuk YAKESMA (tanpa dependensi — Node bawaan).
const http = require("http");
const fs = require("fs");
const path = require("path");

const ROOT = __dirname;
const TYPES = {
  ".html": "text/html; charset=utf-8",
  ".png": "image/png",
  ".svg": "image/svg+xml",
  ".js": "text/javascript",
  ".css": "text/css",
  ".ico": "image/x-icon",
  ".json": "application/json",
};

const server = http.createServer((req, res) => {
  let p = decodeURIComponent((req.url || "/").split("?")[0]);
  if (p === "/" || p === "") p = "/index.html";
  else if (p.endsWith("/")) p += "index.html";
  else if (!path.extname(p)) p += "/index.html"; // /admin -> /admin/index.html
  const safe = path.normalize(p).replace(/^(\.\.[/\\])+/, "");
  const file = path.join(ROOT, safe);

  fs.readFile(file, (err, data) => {
    if (err) {
      // fallback ke index.html
      fs.readFile(path.join(ROOT, "index.html"), (e2, d2) => {
        if (e2) {
          res.writeHead(404, { "Content-Type": "text/plain" });
          res.end("Not found");
        } else {
          res.writeHead(200, { "Content-Type": TYPES[".html"] });
          res.end(d2);
        }
      });
      return;
    }
    res.writeHead(200, {
      "Content-Type": TYPES[path.extname(file)] || "application/octet-stream",
    });
    res.end(data);
  });
});

const PORT = process.env.PORT || 5190;
server.listen(PORT, "127.0.0.1", () => {
  console.log("YAKESMA berjalan di http://127.0.0.1:" + PORT);
});
