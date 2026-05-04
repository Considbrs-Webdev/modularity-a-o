import { defineConfig } from "vite";
import { resolve } from "node:path";
import { fileURLToPath } from "node:url";
import { manifestPlugin } from "vite-plugin-simple-manifest";

const __dirname = fileURLToPath(new URL(".", import.meta.url));

const entries = {
	"css/modularity-a-o": resolve(__dirname, "source/sass/modularity-a-o.scss"),
};

export default defineConfig(({ mode }) => {
	const isProduction = mode === "production";

	return {
		build: {
			outDir: "assets/dist",
			emptyOutDir: true,
			rollupOptions: {
				input: entries,
				output: {
					entryFileNames: isProduction ? "[name].[hash].js" : "[name].js",
					chunkFileNames: isProduction ? "[name].[hash].js" : "[name].js",
					assetFileNames: (assetInfo) => {
						const n = assetInfo.names?.[0] ?? assetInfo.name;
						const base = typeof n === "string" ? n : "";
						if (base.endsWith(".css")) {
							return isProduction ? "[name].[hash][extname]" : "[name][extname]";
						}
						return "assets/[name].[hash][extname]";
					},
				},
			},
			minify: isProduction ? "esbuild" : false,
			sourcemap: true,
		},
		esbuild: {
			keepNames: true,
			minifyIdentifiers: false,
		},
		css: {
			preprocessorOptions: {
				scss: {
					api: "modern-compiler",
					includePaths: ["node_modules", "source"],
					importers: [
						{
							findFileUrl(url) {
								if (url.startsWith("~")) {
									return new URL(url.slice(1), new URL("node_modules/", import.meta.url));
								}
								return null;
							},
						},
					],
				},
			},
		},
		resolve: {
			extensions: [".tsx", ".ts", ".js", ".scss", ".css"],
			alias: {
				"~": resolve(__dirname, "node_modules"),
			},
		},
		plugins: [manifestPlugin("manifest.json")],
	};
});
