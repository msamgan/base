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
import DeleteEntityForm from '@/Components/layout/DeleteEntityForm.jsx'
import { permissions } from '@/Utils/permissions/index.js'
import { destroy, roles as rcRoles, show } from '@actions/RoleController.js'
import { permissions as pcPermissions } from '@actions/PermissionController.js'
import usePermissions from '@/Hooks/usePermissions'

export default function Index({ auth }) {
    const { can } = usePermissions()

    const [roles, setRoles] = useState([])
    const [role, setRole] = useState(null)
    const [data, setData] = useState([])
    const [pageData, setPageData] = useState(pageObject(null))
    const [loading, setLoading] = useState(false)
    const [permissionsList, setPermissionsList] = useState([])

    const getPermissions = async () => setPermissionsList(await pcPermissions.data({}))

    const getRoles = async () => setRoles(await rcRoles.data({}))

    const getRole = async (id) => setRole(await show.data({ params: { role: id } }))

    const processRole = (role) => {
        return {
            Name: <Name value={role.display_name} />,
            UserCount: role.users_count,
            Status: <ActiveBadge value={'Active'} />,
            Actions: (
                <Actions
                    edit={
                        can(permissions.role.update) ? (
                            <OffCanvasButton
                                onClick={() => getRole(role.id).then()}
                                className={'dropdown-item'}
                                id="roleFormCanvas"
                            >
                                <i className="ri-pencil-line me-1 text-primary"></i> Edit
                            </OffCanvasButton>
                        ) : null
                    }
                    deleteAction={
                        can(permissions.role.delete) ? (
                            <DeleteEntityForm
                                action={destroy.route({ role: role.id })}
                                refresh={getRoles}
                                className={'dropdown-item'}
                            />
                        ) : null
                    }
                />
            ),
        }
    }

    useEffect(() => {
        if (can(permissions.role.list)) {
            getRoles().then()
        }

        getPermissions().then()
    }, [])

    useEffect(() => setData(roles.map((role) => processRole(role))), [roles])

    useEffect(() => setPageData(pageObject(role)), [role])

    return (
        <Master user={auth.user}>
            <Head title="Roles" />

            <PageHeader
                title={'Roles'}
                subtitle={'Find all of your business’s roles and there associated permissions.'}
                action={
                    can(permissions.role.create) && (
                        <OffCanvasButton
                            onClick={() => {
                                setRole(null)
                                setPageData(pageObject(null))
                            }}
                            id="roleFormCanvas"
                        >
                            <i className="ri-add-line me-2"></i>
                            Create Role
                        </OffCanvasButton>
                    )
                }
            ></PageHeader>

            {can(permissions.role.create) && (
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
