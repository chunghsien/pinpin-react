import React from 'react';

const pageListRenderer = ({ pages, onPageChange }) => {

    const onClick = (e, page) => {
        e.preventDefault();
        e.target.blur();
        onPageChange(page);
    }

    return (
        <nav className={screen.width < 1201 ? 'react-bootstrap-table-pagination-list col-12 col-md-6 mt-1' : 'react-bootstrap-table-pagination-list col-12 col-md-6'}>
            <ul className="pagination react-bootstrap-table-page-btns-ul">
                {
                    pages.map((page) => {
                        const classes = ['page-item', page.active ? 'active' : null];
                        let symbol = page.page;
                        if (Number.isInteger(symbol) === false) {
                            switch (symbol) {
                                case '>':
                                    symbol = (<i className="fas fa-angle-right" />);
                                    break;
                                case '>>':
                                    symbol = (<i className="fas fa-angle-double-right" />);
                                    break;
                                case '<':
                                    symbol = (<i className="fas fa-angle-left" />);
                                    break;
                                case '<<':
                                    symbol = (<i className="fas fa-angle-double-left" />);
                                    break;
                            }
                        }
                        const key = pages.indexOf(page);
                        return (
                            <li key={key} className={classes.join(' ')} title={page.page}><a onClick={(e) => onClick(e, page.page)} className="btn-link page-link" href="#">{symbol}</a></li>
                        )
                    })
                }
            </ul>
        </nav>
    );
};


const paginationOptions = (t) => {
    const customTotal = (from, to, size) => {
        const translateTxt = t('react-bootstrap-table-next-pagination-show-total', {from: from, to: to, size: size});
        //`Showing ${from} to ${to} of ${size} Results`;
        return (
            <span className="ml-1 react-bootstrap-table-pagination-total">
                {t(translateTxt)}
            </span>
        );
    }

    return {
        sizePerPage: 25,
        sizePerPageList: [
            {
                text: '25', value: 25
            },
            {
                text: '50', value: 50
            },
            {
                text: '100', value: 100
            },
        ],
        pageListRenderer,
        showTotal: screen.width >= 768,
        paginationTotalRenderer: customTotal,
    }
}

export default paginationOptions;