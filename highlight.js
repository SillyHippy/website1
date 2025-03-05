document.addEventListener("DOMContentLoaded", () => {
    document.addEventListener("mouseup", () => {
        let selectedText = window.getSelection().toString();
        if (selectedText.length > 0) {
            highlightSelection();
        }
    });

    function highlightSelection() {
        let selection = window.getSelection();
        let range = selection.getRangeAt(0);
        let span = document.createElement("span");
        span.className = "highlighted-text";
        range.surroundContents(span);
        selection.removeAllRanges();
    }
});
