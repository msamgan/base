import { useEffect, useState } from 'react'
import DependentMenu from '@/Components/layout/DependentMenu.jsx'
import IndependentMenu from '@/Components/layout/IndependentMenu.jsx'
import { index } from '@actions/MenuController'

export default function TopMenu() {
    const [menuItems, setMenuItems] = useState([])

    const getMenus = async () => setMenuItems(await index.data({}))

    useEffect(() => {
        getMenus().then()
    }, [])

    return (
        <aside id="layout-menu" className="layout-menu-horizontal menu-horizontal menu bg-menu-theme flex-grow-0">
            <div className="container-xxl d-flex h-100">
                <ul className="menu-inner">
                    <IndependentMenu item={{ route: 'dashboard', icon: 'ri-home-smile-line', label: 'Dashboard' }} />
                    {Object.keys(menuItems).map((itemKey, index) =>
                        itemKey === ''
                            ? menuItems[itemKey].map((item, index) => <IndependentMenu key={index} item={item} />)
                            : menuItems[itemKey].length > 0 && (
                                  <DependentMenu key={index} menuItems={menuItems} itemKey={itemKey} />
                              ),
                    )}
                </ul>
            </div>
        </aside>
    )
}
