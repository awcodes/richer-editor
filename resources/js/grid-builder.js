import { Node } from "@tiptap/core";
import { Plugin, PluginKey, TextSelection } from "@tiptap/pm/state";

const BUTTON_DEFS = [
    {
        label: ' \u2190 Merge',
        command: 'mergeColumnLeft',
        hideWhenFirst: true,
        hideWhenLast: false,
        hideWhenSpanOne: false,
    },
    {
        label: 'Add Before',
        command: 'addColumnBefore',
        hideWhenFirst: false,
        hideWhenLast: false,
        hideWhenSpanOne: false,
    },
    {
        label: 'Add After',
        command: 'addColumnAfter',
        hideWhenFirst: false,
        hideWhenLast: false,
        hideWhenSpanOne: false,
    },
    {
        label: 'Delete',
        command: 'deleteColumn',
        hideWhenFirst: false,
        hideWhenLast: false,
        hideWhenSpanOne: false,
    },
    {
        label: 'Split',
        command: 'splitColumn',
        hideWhenFirst: false,
        hideWhenLast: false,
        hideWhenSpanOne: true,
    },
    {
        label: 'Merge \u2192',
        command: 'mergeColumnRight',
        hideWhenFirst: false,
        hideWhenLast: true,
        hideWhenSpanOne: false,
    },
]

function findParentNodeOfType(nodeType) {
    return (selection) => {
        const { $from } = selection;
        for (let depth = $from.depth; depth > 0; depth--) {
            const node = $from.node(depth);
            if (node.type === nodeType) {
                return {
                    pos: $from.before(depth),
                    node,
                    depth,
                };
            }
        }
        return undefined;
    };
}

function getColumnIndex(gridNode, relativeOffset) {
    let offset = 0;
    for (let i = 0; i < gridNode.childCount; i++) {
        if (offset === relativeOffset) return i;
        offset += gridNode.child(i).nodeSize;
    }
    return -1;
}

export default Node.create({
    name: "gridBuilder",

    group: "block",

    defining: true,

    isolating: true,

    content: "gridBuilderColumn+",

    addOptions() {
        return {
            HTMLAttributes: {
                class: "grid-builder",
            },
        };
    },

    addAttributes() {
        return {
            "data-cols": {
                default: "2",
                parseHTML: (element) => element.getAttribute("data-cols") || "2",
            },
            "data-from-breakpoint": {
                default: "md",
                parseHTML: (element) =>
                    element.getAttribute("data-from-breakpoint") || "md",
            },
            style: {
                default: null,
                renderHTML: (attributes) => {
                    const cols = attributes["data-cols"] || 2;
                    return {
                        style: `grid-template-columns: repeat(${cols}, minmax(0, 1fr));`,
                    };
                },
            },
        };
    },

    parseHTML() {
        return [
            {
                tag: "div.grid-builder",
            },
        ];
    },

    renderHTML({ HTMLAttributes }) {
        const cols = HTMLAttributes["data-cols"] || 2;
        return [
            "div",
            {
                ...this.options.HTMLAttributes,
                "data-cols": cols,
                "data-from-breakpoint": HTMLAttributes["data-from-breakpoint"] || "md",
                style: `grid-template-columns: repeat(${cols}, minmax(0, 1fr));`,
            },
            0,
        ];
    },

    addCommands() {
        return {
            insertGridBuilder:
                ({ columns = [1, 1], fromBreakpoint = "md" } = {}) =>
                    ({ state, tr, dispatch }) => {
                        const gridBuilderColumnType =
                            state.schema.nodes.gridBuilderColumn;
                        const paragraphType = state.schema.nodes.paragraph;

                        if (!gridBuilderColumnType || !paragraphType) {
                            return false;
                        }

                        const totalCols = columns.reduce((a, b) => a + b, 0);

                        const columnNodes = columns.map((span) =>
                            gridBuilderColumnType.create(
                                { "data-col-span": String(span) },
                                paragraphType.create()
                            )
                        );

                        const gridNode = this.type.create(
                            {
                                "data-cols": String(totalCols),
                                "data-from-breakpoint": fromBreakpoint,
                            },
                            columnNodes
                        );

                        if (dispatch) {
                            const { selection } = tr;
                            const insertPos = selection.from;
                            tr.replaceRangeWith(insertPos, selection.to, gridNode);
                            // Place cursor inside the first column's paragraph
                            const $pos = tr.doc.resolve(insertPos + 1);
                            tr.setSelection(TextSelection.near($pos));
                        }

                        return true;
                    },

            addColumnBefore:
                () =>
                    ({ state, tr, dispatch }) => {
                        const gridBuilderColumnType =
                            state.schema.nodes.gridBuilderColumn;
                        const gridBuilderType = state.schema.nodes.gridBuilder;
                        const paragraphType = state.schema.nodes.paragraph;

                        const colResult = findParentNodeOfType(gridBuilderColumnType)(
                            state.selection
                        );
                        const gridResult = findParentNodeOfType(gridBuilderType)(
                            state.selection
                        );

                        if (!colResult || !gridResult) return false;

                        if (dispatch) {
                            const newCol = gridBuilderColumnType.create(
                                { "data-col-span": "1" },
                                paragraphType.create()
                            );

                            tr.insert(colResult.pos, newCol);

                            const currentCols =
                                parseInt(gridResult.node.attrs["data-cols"]) || 2;
                            tr.setNodeMarkup(gridResult.pos, null, {
                                ...gridResult.node.attrs,
                                "data-cols": String(currentCols + 1),
                            });
                        }

                        return true;
                    },

            addColumnAfter:
                () =>
                    ({ state, tr, dispatch }) => {
                        const gridBuilderColumnType =
                            state.schema.nodes.gridBuilderColumn;
                        const gridBuilderType = state.schema.nodes.gridBuilder;
                        const paragraphType = state.schema.nodes.paragraph;

                        const colResult = findParentNodeOfType(gridBuilderColumnType)(
                            state.selection
                        );
                        const gridResult = findParentNodeOfType(gridBuilderType)(
                            state.selection
                        );

                        if (!colResult || !gridResult) return false;

                        if (dispatch) {
                            const newCol = gridBuilderColumnType.create(
                                { "data-col-span": "1" },
                                paragraphType.create()
                            );

                            const afterPos =
                                colResult.pos + colResult.node.nodeSize;
                            tr.insert(afterPos, newCol);

                            const currentCols =
                                parseInt(gridResult.node.attrs["data-cols"]) || 2;
                            tr.setNodeMarkup(gridResult.pos, null, {
                                ...gridResult.node.attrs,
                                "data-cols": String(currentCols + 1),
                            });
                        }

                        return true;
                    },

            deleteColumn:
                () =>
                    ({ state, tr, dispatch }) => {
                        const gridBuilderColumnType =
                            state.schema.nodes.gridBuilderColumn;
                        const gridBuilderType = state.schema.nodes.gridBuilder;

                        const colResult = findParentNodeOfType(gridBuilderColumnType)(
                            state.selection
                        );
                        const gridResult = findParentNodeOfType(gridBuilderType)(
                            state.selection
                        );

                        if (!colResult || !gridResult) return false;

                        if (dispatch) {
                            const colSpan =
                                parseInt(colResult.node.attrs["data-col-span"]) || 1;

                            if (gridResult.node.childCount <= 1) {
                                tr.delete(
                                    gridResult.pos,
                                    gridResult.pos + gridResult.node.nodeSize
                                );
                            } else {
                                tr.delete(
                                    colResult.pos,
                                    colResult.pos + colResult.node.nodeSize
                                );

                                const currentCols =
                                    parseInt(gridResult.node.attrs["data-cols"]) || 2;
                                tr.setNodeMarkup(gridResult.pos, null, {
                                    ...gridResult.node.attrs,
                                    "data-cols": String(
                                        Math.max(1, currentCols - colSpan)
                                    ),
                                });
                            }
                        }

                        return true;
                    },

            mergeColumnRight:
                () =>
                    ({ state, tr, dispatch }) => {
                        const gridBuilderColumnType =
                            state.schema.nodes.gridBuilderColumn;
                        const gridBuilderType = state.schema.nodes.gridBuilder;

                        const colResult = findParentNodeOfType(gridBuilderColumnType)(
                            state.selection
                        );
                        const gridResult = findParentNodeOfType(gridBuilderType)(
                            state.selection
                        );

                        if (!colResult || !gridResult) return false;

                        const colIndex = getColumnIndex(
                            gridResult.node,
                            colResult.pos - gridResult.pos - 1
                        );

                        if (colIndex >= gridResult.node.childCount - 1) return false;

                        if (dispatch) {
                            const rightCol = gridResult.node.child(colIndex + 1);
                            const rightColPos = colResult.pos + colResult.node.nodeSize;

                            const currentSpan =
                                parseInt(colResult.node.attrs["data-col-span"]) || 1;
                            const rightSpan =
                                parseInt(rightCol.attrs["data-col-span"]) || 1;

                            const insertPos =
                                colResult.pos + colResult.node.content.size;
                            const contentSlice = rightCol.content;
                            contentSlice.forEach((child, offset) => {
                                tr.insert(insertPos + offset, child);
                            });

                            const newRightColPos =
                                rightColPos + rightCol.content.size;
                            tr.delete(
                                newRightColPos,
                                newRightColPos + rightCol.nodeSize
                            );

                            tr.setNodeMarkup(colResult.pos, null, {
                                ...colResult.node.attrs,
                                "data-col-span": String(currentSpan + rightSpan),
                            });
                        }

                        return true;
                    },

            mergeColumnLeft:
                () =>
                    ({ state, tr, dispatch }) => {
                        const gridBuilderColumnType =
                            state.schema.nodes.gridBuilderColumn;
                        const gridBuilderType = state.schema.nodes.gridBuilder;

                        const colResult = findParentNodeOfType(gridBuilderColumnType)(
                            state.selection
                        );
                        const gridResult = findParentNodeOfType(gridBuilderType)(
                            state.selection
                        );

                        if (!colResult || !gridResult) return false;

                        const colIndex = getColumnIndex(
                            gridResult.node,
                            colResult.pos - gridResult.pos - 1
                        );

                        if (colIndex <= 0) return false;

                        if (dispatch) {
                            const leftCol = gridResult.node.child(colIndex - 1);
                            const leftColPos =
                                colResult.pos - leftCol.nodeSize;

                            const currentSpan =
                                parseInt(colResult.node.attrs["data-col-span"]) || 1;
                            const leftSpan =
                                parseInt(leftCol.attrs["data-col-span"]) || 1;

                            const insertPos = leftColPos + leftCol.content.size;
                            const contentSlice = colResult.node.content;
                            contentSlice.forEach((child, offset) => {
                                tr.insert(insertPos + offset, child);
                            });

                            const newCurrentPos =
                                colResult.pos + colResult.node.content.size;
                            tr.delete(
                                newCurrentPos,
                                newCurrentPos + colResult.node.nodeSize
                            );

                            tr.setNodeMarkup(leftColPos, null, {
                                ...leftCol.attrs,
                                "data-col-span": String(leftSpan + currentSpan),
                            });
                        }

                        return true;
                    },

            splitColumn:
                () =>
                    ({ state, tr, dispatch }) => {
                        const gridBuilderColumnType =
                            state.schema.nodes.gridBuilderColumn;
                        const gridBuilderType = state.schema.nodes.gridBuilder;
                        const paragraphType = state.schema.nodes.paragraph;

                        const colResult = findParentNodeOfType(gridBuilderColumnType)(
                            state.selection
                        );
                        const gridResult = findParentNodeOfType(gridBuilderType)(
                            state.selection
                        );

                        if (!colResult || !gridResult) return false;

                        const span =
                            parseInt(colResult.node.attrs["data-col-span"]) || 1;

                        if (span <= 1) return false;

                        if (dispatch) {
                            const newColumns = [];

                            newColumns.push(
                                gridBuilderColumnType.create(
                                    { "data-col-span": "1" },
                                    colResult.node.content
                                )
                            );

                            for (let i = 1; i < span; i++) {
                                newColumns.push(
                                    gridBuilderColumnType.create(
                                        { "data-col-span": "1" },
                                        paragraphType.create()
                                    )
                                );
                            }

                            tr.replaceWith(
                                colResult.pos,
                                colResult.pos + colResult.node.nodeSize,
                                newColumns
                            );
                        }

                        return true;
                    },
        };
    },

    addProseMirrorPlugins() {
        const editor = this.editor;

        return [
            new Plugin({
                key: new PluginKey("gridBuilderBubbleMenu"),
                view(editorView) {
                    const menu = document.createElement("div");
                    menu.className = "grid-builder-bubble-menu";
                    menu.style.display = "none";
                    menu.style.position = "absolute";

                    const buttons = BUTTON_DEFS.map((btn) => {
                        const button = document.createElement("button");
                        button.type = "button";
                        button.className = "grid-builder-bubble-btn";
                        button.dataset.command = btn.command;
                        button.textContent = btn.label;
                        button.addEventListener("mousedown", (e) => {
                            e.preventDefault();
                            e.stopPropagation();
                            editor.chain().focus()[btn.command]().run();
                        });
                        menu.appendChild(button);
                        return { ...btn, el: button };
                    });

                    const wrapper = editorView.dom.parentElement;
                    if (wrapper) {
                        wrapper.style.position = "relative";
                        wrapper.appendChild(menu);
                    }

                    return {
                        update(view) {
                            const { state } = view;
                            const gridBuilderColumnType =
                                state.schema.nodes.gridBuilderColumn;
                            const gridBuilderType =
                                state.schema.nodes.gridBuilder;

                            if (!gridBuilderColumnType || !gridBuilderType) {
                                menu.style.display = "none";
                                return;
                            }

                            const colResult =
                                findParentNodeOfType(gridBuilderColumnType)(
                                    state.selection
                                );
                            const gridResult =
                                findParentNodeOfType(gridBuilderType)(
                                    state.selection
                                );

                            if (!colResult || !gridResult) {
                                menu.style.display = "none";
                                return;
                            }

                            let colDom = view.nodeDOM(colResult.pos);
                            if (
                                !colDom ||
                                !colDom.classList?.contains("grid-builder-col")
                            ) {
                                try {
                                    const domInfo = view.domAtPos(
                                        state.selection.from
                                    );
                                    colDom =
                                        domInfo.node.nodeType === 1
                                            ? domInfo.node
                                            : domInfo.node.parentElement;
                                    while (
                                        colDom &&
                                        !colDom.classList?.contains(
                                            "grid-builder-col"
                                        )
                                    ) {
                                        colDom = colDom.parentElement;
                                    }
                                } catch (e) {
                                    colDom = null;
                                }
                            }

                            if (!colDom) {
                                menu.style.display = "none";
                                return;
                            }

                            // Measure while displayed so offsetHeight is accurate.
                            menu.style.display = "flex";

                            // Position above the active column, flipping below when the
                            // wrapper (which may be a clipping scroll container) has no room.
                            const gap = 8;
                            const colRect = colDom.getBoundingClientRect();
                            const wrapperRect = wrapper.getBoundingClientRect();
                            const menuHeight = menu.offsetHeight;
                            const fitsAbove =
                                colRect.top - menuHeight - gap >= wrapperRect.top;
                            const fitsBelow =
                                colRect.bottom + menuHeight + gap <= wrapperRect.bottom;

                            let top;
                            if (fitsAbove) {
                                top = colRect.top - wrapperRect.top - menuHeight - gap;
                            } else if (fitsBelow) {
                                top = colRect.bottom - wrapperRect.top + gap;
                            } else {
                                top = gap;
                            }

                            menu.style.top = `${top + wrapper.scrollTop}px`;
                            menu.style.left = `${colRect.left - wrapperRect.left + wrapper.scrollLeft}px`;

                            const colIndex = getColumnIndex(
                                gridResult.node,
                                colResult.pos - gridResult.pos - 1
                            );

                            const isFirst = colIndex === 0;
                            const isLast =
                                colIndex >= gridResult.node.childCount - 1;
                            const span =
                                parseInt(
                                    colResult.node.attrs["data-col-span"]
                                ) || 1;

                            buttons.forEach((btnDef) => {
                                let hidden = false;
                                if (btnDef.hideWhenFirst && isFirst)
                                    hidden = true;
                                if (btnDef.hideWhenLast && isLast)
                                    hidden = true;
                                if (btnDef.hideWhenSpanOne && span <= 1)
                                    hidden = true;

                                btnDef.el.style.display = hidden ? "none" : "";
                            });
                        },

                        destroy() {
                            menu.remove();
                        },
                    };
                },
            }),
        ];
    },
});
