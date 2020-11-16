import loadable from '@loadable/component';

const DefaultContainer = loadable(() => import('./views/admin/pages/defaults/DefaultContainer'));
const pageConfig = window.pageConfig;
let _routes = [/*{ path: '/admin/', exact: true, strict: true, name: 'Home' }*/];


pageConfig.routes.map((item) => {
    let _component = null;
   
    if (item.component) {
        _component = loadable(() => import(item.component + '.js'));
    } else {
        if (item.uri != '/admin/') {
            _component = DefaultContainer;
        }
    }
    let _push = {
        path: item.uri.replace(/\/\:method_or_id$/, ''),
        name: item.name,
    };
    if (item.uri != '/admin/') {
        _push.component = _component;
        //_push.strict = true;
    } else {
        _push.exact = true;
    }
    _routes.push(_push);
});
const routes = _routes;
export default routes;