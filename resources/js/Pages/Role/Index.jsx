import Master from '@/Layouts/Master.jsx'
import { Head } from '@inertiajs/react'
import PageHeader from '@/Components/PageHeader.jsx'
import OffCanvasButton from '@/Components/off_canvas/OffCanvasButton.jsx'
import OffCanvas from '@/Components/off_canvas/OffCanvas.jsx'
import Table from '@/Components/layout/Table.jsx'
import { pageObject } from '@/Pages/Role/helper.js'
import Form from '@/Pages/Role/Partials/Form.jsx'
import { useEffect, useState } from 'react'
import Name from '@/Components/helpers/Name.jsx'
import ActiveBadge from '@/Components/helpers/ActiveBadge.jsx'
import Actions from '@/Components/helpers/Actions.jsx'
import { permissions } from '@/Utils/permissions/index.js'
import { destroy, roles as _roles, show } from '@actions/RoleController.js'
import { permissions as _permissions } from '@actions/PermissionController.js'
import usePermissions from '@/Hooks/usePermissions'
import EditActionButton from '@/Components/EditActionButton.jsx'
import DeleteActionButton from '@/Components/DeleteActionButton.jsx'
import CreateActionButton from '@/Components/CreateActionButton.jsx'

export default function Index() {
    const { can } = usePermissions()

    const [roles, setRoles] = useState([])
    const [role, setRole] = useState(null)
    const [data, setData] = useState([])
    const [pageData, setPageData] = useState(pageObject(null))
    const [loading, setLoading] = useState(true)
    const [permissionsList, setPermissionsList] = useState([])

    const getPermissions = async () => setPermissionsList(await _permissions.data({}))

    const getRoles = async () => setRoles(await _roles.data({}))

    const getRole = async (id) => setRole(await show.data({ params: { role: id } }))

    const editRole = (role) => {
        getRole(role.id).then()
        setPageData(pageObject(role))
    }

    const processRole = (role) => {
        return {
            Name: <Name value={role.display_name} />,
            UserCount: role.users_count,
            Status: <ActiveBadge value={'Active'} />,
            Actions: (
                <Actions
                    edit={<EditActionButton module={'role'} onClick={() => editRole(role)} />}
                    deleteAction={
                        <DeleteActionButton
                            module={'role'}
                            route={destroy.route({ role: role.id })}
                            refresh={getRoles}
                        />
                    }
                />
            ),
        }
    }

    useEffect(() => {
        if (can(permissions.role.list)) {
            getRoles()
                .then()
                .finally(() => setLoading(false))
        }

        getPermissions().then()
    }, [])

    useEffect(() => setData(roles.map((role) => processRole(role))), [roles])

    return (
        <Master>
            <Head title="Roles" />

            <PageHeader
                title={'Roles'}
                subtitle={'Find all of your business’s roles and there associated permissions.'}
                action={
                    <CreateActionButton
                        module={'role'}
                        onClick={() => {
                            setRole(null)
                            setPageData(pageObject(null))
                        }}
                    />
                }
            ></PageHeader>

            {can([permissions.role.view, permissions.role.update, permissions.role.create]) && (
                <OffCanvas id="roleFormCanvas" title={pageData.title}>
                    <Form getRoles={getRoles} role={role} permissionsList={permissionsList} />
                </OffCanvas>
            )}

            <div className="col-12">
                <Table data={data} loading={loading} permission={can(permissions.role.list)} />
            </div>
        </Master>
    )
}
