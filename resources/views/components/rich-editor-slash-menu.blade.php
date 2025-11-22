<div
    data-slash-menu-items="@js($getItems())"
    style="display: none;"
    x-on:handle-slash-menu-command="(event) => eval(event.detail.action)"
></div>
