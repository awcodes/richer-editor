import { Node } from "@tiptap/core";

export default Node.create({
    name: "gridBuilderColumn",

    content: "block+",

    isolating: true,

    addOptions() {
        return {
            HTMLAttributes: {
                class: "grid-builder-col",
            },
        };
    },

    addAttributes() {
        return {
            "data-col-span": {
                default: "1",
                parseHTML: (element) => element.getAttribute("data-col-span") || "1",
            },
            style: {
                default: null,
                renderHTML: (attributes) => {
                    const span = attributes["data-col-span"] || 1;
                    return { style: `grid-column: span ${span};` };
                },
            },
        };
    },

    parseHTML() {
        return [
            {
                tag: "div.grid-builder-col",
            },
        ];
    },

    renderHTML({ HTMLAttributes }) {
        const span = HTMLAttributes["data-col-span"] || 1;
        return [
            "div",
            {
                ...this.options.HTMLAttributes,
                "data-col-span": span,
                style: `grid-column: span ${span};`,
            },
            0,
        ];
    },
});
