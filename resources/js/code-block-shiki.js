import CodeBlockShiki from 'tiptap-extension-code-block-shiki'
import { bundledLanguagesInfo } from 'shiki'

// Config is passed from the PHP `CodeBlockShikiPlugin` as query params on this
// module's URL (Filament loads custom extensions by URL and calls the default
// export with no arguments, so the URL is the only channel for per-plugin config).
const params = new URL(import.meta.url).searchParams

const defaultTheme = params.get('defaultTheme') ?? 'tokyo-night'
const light = params.get('lightTheme')
const dark = params.get('darkTheme')

const options = { defaultTheme }

if (light && dark) {
    options.themes = { light, dark }
}

// Languages shown in the per-block dropdown. Defaults to every language Shiki
// bundles; the PHP plugin can pass a curated, comma-separated `languages` list.
const namesById = new Map(bundledLanguagesInfo.map(({ id, name }) => [id, name]))
const languagesParam = params.get('languages')
const languages = languagesParam
    ? languagesParam.split(',').map((id) => ({ id, name: namesById.get(id) ?? id }))
    : bundledLanguagesInfo.map(({ id, name }) => ({ id, name }))

function buildLanguageSelect(currentLanguage) {
    const select = document.createElement('select')
    select.classList.add('richer-shiki-language-select')
    select.contentEditable = 'false'

    const plain = document.createElement('option')
    plain.value = ''
    plain.textContent = 'Plain Text'
    select.append(plain)

    for (const { id, name } of languages) {
        const option = document.createElement('option')
        option.value = id
        option.textContent = name
        select.append(option)
    }

    select.value = currentLanguage ?? ''

    return select
}

export default () =>
    CodeBlockShiki.extend({
        addNodeView() {
            return ({ node, editor, getPos }) => {
                // The node view's outer element is the `<pre>` so the extension's
                // `.shiki` node decoration (which carries the theme background)
                // lands on it directly, instead of on a wrapper that wouldn't
                // match the code's background.
                const pre = document.createElement('pre')
                pre.classList.add('richer-shiki-code-block')

                const select = buildLanguageSelect(node.attrs.language)

                select.addEventListener('change', (event) => {
                    if (typeof getPos !== 'function') {
                        return
                    }

                    editor.view.dispatch(
                        editor.view.state.tr.setNodeAttribute(
                            getPos(),
                            'language',
                            event.target.value || null,
                        ),
                    )

                    editor.view.focus()
                })

                const code = document.createElement('code')

                pre.append(select, code)

                return {
                    dom: pre,
                    contentDOM: code,
                    update: (updatedNode) => {
                        if (updatedNode.type !== node.type) {
                            return false
                        }

                        const language = updatedNode.attrs.language ?? ''

                        if (select.value !== language) {
                            select.value = language
                        }

                        return true
                    },
                    ignoreMutation: (mutation) =>
                        mutation.type !== 'selection' && ! code.contains(mutation.target),
                }
            }
        },
    }).configure(options)
