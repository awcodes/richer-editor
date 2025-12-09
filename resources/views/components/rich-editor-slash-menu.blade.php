<div
    data-slash-menu-items="@js($getItems())"
    data-slash-menu-no-results="{{ $getNoResultsMessage() }}"
    style="display: none;"
    x-on:handle-slash-menu-command="(event) => eval(event.detail.action)"
></div>
