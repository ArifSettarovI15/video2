module.exports = {
    modernizr: {
        "customTests": [],
        "dest": "build/js/libs/modernizr.js",
        "tests": [
            "geolocation",
            "touchevents",
            "svgforeignobject",
            "inlinesvg"
        ],
        "options": [
            "setClasses"
        ],
        "uglify": true
    }
};