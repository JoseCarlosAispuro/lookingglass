import fs from "fs";
import path from "path";

const samplePath = path.join("../../../", "wp-config-sample.php");
const targetPath = path.join("../../../", "wp-config.php");

if (!fs.existsSync(samplePath)) {
    console.error("❌ wp-config-sample.php not found");
    process.exit(1);
}

if (fs.existsSync(targetPath)) {
    console.error("⚠️ wp-config.php already exists. Aborting.");
    process.exit(1);
}

let content = fs.readFileSync(samplePath, "utf8");

const replacements = {
    database_name_here: "wordpress",
    username_here: "wp",
    password_here: "wp_password",
    localhost: "127.0.0.1",
};

for (const [search, replace] of Object.entries(replacements)) {
    content = content.replace(
        new RegExp(`['"]${search}['"]`, "g"),
        `'${replace}'`
    );
}

// Force charset + collate (por si el sample cambia)
content = content.replace(
    /define\(\s*'DB_CHARSET'.*\);/,
    "define('DB_CHARSET', 'utf8mb4');"
);

content = content.replace(
    /define\(\s*'DB_COLLATE'.*\);/,
    "define('DB_COLLATE', '');"
);

fs.writeFileSync(targetPath, content, "utf8");

console.log("✅ wp-config.php created successfully");
