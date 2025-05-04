const toTitleCase = (str) => {
    return str
        .replace(/([A-Z])/g, ' $1')
        .trim()
        .split(' ')
        .map((word) => word.charAt(0).toUpperCase() + word.slice(1))
        .join(' ')
}

const toCamelCase = (str) => {
    return str.replace(/[-_.](\w)/g, (_, c) => c.toUpperCase()).replace(/^\w/, (c) => c.toLowerCase())
}

const toClassCase = (str) => {
    return str.replace(/[-_.](\w)/g, (_, c) => c.toUpperCase()).replace(/^\w/, (c) => c.toUpperCase())
}

const toUnderscoreCase = (str) => {
    return str
        .replace(/([A-Z])/g, '_$1')
        .toLowerCase()
        .replace(/^_/, '')
        .replace(/[-.]/, '_')
}

const toKebabCase = (str) => {
    return str
        .replace(/([A-Z])/g, '-$1')
        .toLowerCase()
        .replace(/^-/, '')
        .replace(/[_.]/, '-')
}

const toDotCase = (str) => {
    return str
        .replace(/([A-Z])/g, '.$1')
        .toLowerCase()
        .replace(/^\./, '')
        .replace(/[-_]/, '.')
}

const pluralize = (str) => str + 's'

export default (text) => {
    return {
        titleCase: toTitleCase(text),
        camelCase: toCamelCase(text),
        classCase: toClassCase(text),
        underscoreCase: toUnderscoreCase(text),
        kebabCase: toKebabCase(text),
        dotCase: toDotCase(text),
        camelCasePlural: pluralize(toCamelCase(text)),
        underscoreCasePlural: pluralize(toUnderscoreCase(text)),
        classCasePlural: pluralize(toClassCase(text)),
    }
}
