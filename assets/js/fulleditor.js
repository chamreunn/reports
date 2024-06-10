// Initialize Quill with custom toolbar options
var toolbarOptions = [
    ['bold', 'italic', 'underline', 'strike'], // toggled buttons
    [{
        'header': 1
    }, {
        'header': 2
    }], // custom button values
    [{
        'list': 'ordered'
    }, {
        'list': 'bullet'
    }],
    [{
        'script': 'sub'
    }, {
        'script': 'super'
    }], // superscript/subscript
    [{
        'indent': '-1'
    }, {
        'indent': '+1'
    }], // outdent/indent
    [{
        'direction': 'rtl'
    }], // text direction
    [{
        'size': ['small', false, 'large', 'huge']
    }], // custom dropdown
    [{
        'header': [1, 2, 3, 4, 5, 6, false]
    }],
    [{
        'color': []
    }, {
        'background': []
    }], // dropdown with defaults from theme
    [{
        'font': ['Khmer MEF1', 'Khmer MEF2', 'sans-serif']
    }], // Add Khmer fonts to font dropdown
    [{
        'align': []
    }],
    ['link', 'image', 'video'], // link and image, video
    ['clean'], // remove formatting button
];

// Initialize Quill Editor with Snow Theme
var quill = new Quill('#quill-editor', {
    theme: 'snow', // Use Snow theme
    placeholder: 'សូមវាយបញ្ចូល...', // Specify placeholder text
    modules: {
        toolbar: toolbarOptions // Include custom toolbar options
    }
});
