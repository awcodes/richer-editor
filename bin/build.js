import esbuild from 'esbuild'

const isDev = process.argv.includes('--dev')

// Redirect TipTap/ProseMirror imports to the instance bundled by Filament's
// rich editor, exposed at window.FilamentRichEditor.tiptap. This avoids
// duplicate bundling and prevents ProseMirror instanceof mismatches.
// https://filamentphp.com/docs/5.x/forms/rich-editor#sharing-the-bundled-tiptapprosemirror-instance
const tiptapSharedPlugin = {
    name: 'tiptap-shared',
    setup(build) {
        const keys = {
            '@tiptap/core': 'core',
            '@tiptap/pm/state': 'pmState',
            '@tiptap/pm/view': 'pmView',
            '@tiptap/pm/model': 'pmModel',
        }

        build.onResolve({ filter: /^@tiptap\/(core|pm\/(state|view|model))$/ }, (args) => ({
            path: args.path,
            namespace: 'tiptap-shared',
        }))

        build.onLoad({ filter: /.*/, namespace: 'tiptap-shared' }, async (args) => {
            const realModule = await import(args.path)
            const namedExports = Object.keys(realModule).filter(
                (key) => key !== '__esModule' && key !== 'default',
            )

            const key = keys[args.path]
            let code = `const __module = window.FilamentRichEditor.tiptap.${key};\n`

            if (namedExports.length) {
                code += `export const { ${namedExports.join(', ')} } = __module;\n`
            }

            code += `export default __module?.default ?? __module;\n`

            return { contents: code, loader: 'js' }
        })
    },
}

async function compile(options) {
    const context = await esbuild.context(options)

    if (isDev) {
        await context.watch()
    } else {
        await context.rebuild()
        await context.dispose()
    }
}

const defaultOptions = {
    define: {
        'process.env.NODE_ENV': isDev ? `'development'` : `'production'`,
    },
    bundle: true,
    mainFields: ['module', 'main'],
    platform: 'neutral',
    sourcemap: isDev ? 'inline' : false,
    sourcesContent: isDev,
    treeShaking: true,
    target: ['es2020'],
    minify: !isDev,
    plugins: [tiptapSharedPlugin, {
        name: 'watchPlugin',
        setup: function (build) {
            build.onStart(() => {
                console.log(`Build started at ${new Date(Date.now()).toLocaleTimeString()}: ${build.initialOptions.outfile}`)
            })

            build.onEnd((result) => {
                if (result.errors.length > 0) {
                    console.log(`Build failed at ${new Date(Date.now()).toLocaleTimeString()}: ${build.initialOptions.outfile}`, result.errors)
                } else {
                    console.log(`Build finished at ${new Date(Date.now()).toLocaleTimeString()}: ${build.initialOptions.outfile}`)
                }
            })
        }
    }],
}

const extensions = [
    'embed',
    'id',
    'video',
    'fullscreen',
    'link',
    'figure',
    'code-block-shiki',
    'emoji',
    'slash-menu',
]

extensions.forEach((extension) => {
    compile({
        ...defaultOptions,
        entryPoints: [`./resources/js/${extension}.js`],
        outfile: `./resources/dist/${extension}.js`,
    }).then(() => {
        console.log(`Build completed for ${extension}.js`)
    })
})
