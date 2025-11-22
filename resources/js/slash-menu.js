import { Extension } from '@tiptap/core'
import Suggestion from '@tiptap/suggestion'
import getSlashMenuSuggestion from './slash-menu-suggestion.js'
import { PluginKey } from '@tiptap/pm/state'

const pluginKey = new PluginKey('slashMenuSuggestion')

const SlashMenu = Extension.create({
    name: 'slashMenu',

    addOptions() {
        return {
            suggestions: [],
        }
    },

    addProseMirrorPlugins() {
        return [
            Suggestion({
                editor: this.editor,
                char: '/',
                pluginKey,
                ...getSlashMenuSuggestion({
                    noItemsSearchResultsMessage: 'No results found',
                }),
            }),
        ]
    },
})

export default SlashMenu
