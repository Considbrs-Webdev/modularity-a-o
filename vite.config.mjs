import { createViteConfig } from "vite-config-factory";

const entries = {
	"css/modularity-a-o": "./source/sass/modularity-a-o.scss",
};

export default createViteConfig(entries, {
	outDir: "assets/dist",
	manifestFile: "manifest.json",
});
