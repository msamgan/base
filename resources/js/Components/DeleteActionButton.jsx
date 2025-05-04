import usePermissions from '@/Hooks/usePermissions'
import { permissions } from '@/Utils/permissions/index.js'
import DeleteEntityForm from '@/Components/layout/DeleteEntityForm.jsx'

export default function DeleteActionButton({ module, route, refresh }) {
    const { can } = usePermissions()

    return can(permissions[module].delete) ? (
        <DeleteEntityForm action={route} refresh={refresh} className={'dropdown-item'} />
    ) : null
}
