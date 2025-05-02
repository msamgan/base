export const dataObject = (role) => {
    return {
        name: role ? role.display_name : '',
        permissions: role ? role.permissions.map((p) => p.id) : [],
    }
}

export const pageObject = (role) => {
    return {
        title: role ? 'Edit Role' : 'Create Role',
    }
}
