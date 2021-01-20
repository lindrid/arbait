var routes = require('./routes.json');

// используем так route('home', ['1', '2'])
// т.е. имя пути и массив данных для передачи
export default function () {
    // копия массива с аргументами
    var args = Array.prototype.slice.call(arguments);
    var name = args.shift();

    if (routes[name] === undefined) {
        console.log('UNDEFINED ROUTE : ' + name);
    }
    else {
        return routes[name]
            .split('/')
            .map(function (s) {
                if (s[0] == '{') {
                    return '1';
                }
                else {
                    return s;
                }
            })
            .join('/');
    }
}