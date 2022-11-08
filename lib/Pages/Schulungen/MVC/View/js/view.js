function showLehrer(val)
{
    const div = document.createElement('div');
    div.className = "pb-2 mb-2 border__bottom--dotted-gray";

    const checkbox = document.createElement('input');
    checkbox.type = "checkbox";
    checkbox.name = "extern[]";
    checkbox.value = val;

    const label = document.createElement('span');
    label.className = "ps-3";
    const inp = document.createElement('input');
    inp.type = "text";
    inp.className = "invisible-formfield w-75";
    inp.name = "name[]";
    inp.value = val;
    inp.appendChild(document.createTextNode(val));

    const br = document.createElement('br');

    const container = document.getElementById('dspLehrer');
    container.appendChild(div);
    container.appendChild(checkbox);
    container.appendChild(label);
    container.appendChild(inp);
    container.appendChild(br);

    $('#mid').val('');
    $('#mid').prop('required',false);
}