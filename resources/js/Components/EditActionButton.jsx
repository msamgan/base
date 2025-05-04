import usePermissions from '@/Hooks/usePermissions'
import OffCanvasButton from '@/Components/off_canvas/OffCanvasButton.jsx'
import { permissions } from '@/Utils/permissions/index.js'
import caseify from '@/Utils/caseify.js'

export default function EditActionButton({ module, onClick }) {
    const { can } = usePermissions()
    const cases = caseify(module)

    return can([permissions[module].view, permissions[module].update]) ? (
        <OffCanvasButton onClick={onClick} className={'dropdown-item'} id={cases.camelCase + 'FormCanvas'}>
            {can(permissions[module].update) ? (
                <>
                    <i className="ri-pencil-line me-1 text-primary"></i> Edit
                </>
            ) : can(permissions[module].view) ? (
                <>
                    <i className="ri-eye-line me-1 text-primary"></i> view
                </>
            ) : null}
        </OffCanvasButton>
    ) : null
}
