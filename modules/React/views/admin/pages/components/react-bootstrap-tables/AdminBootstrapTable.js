import React, { Suspense, useEffect, useState, useRef } from 'react';
import { useStore, useDispatch } from 'react-redux';
import BootstrapTable from 'react-bootstrap-table-next';
import cellEditFactory from 'react-bootstrap-table2-editor';
import paginationFactory from 'react-bootstrap-table2-paginator';
import filterFactory from 'react-bootstrap-table2-filter';
import animateScrollTo from 'animated-scroll-to';
import { notify, toConfirm } from '../alertify';
import { Link } from "react-router-dom";
import Loading from 'react-fullscreen-loading';

const AdminBootstrapTable = (props) => {
  const dispatch = useDispatch();
  const store = useStore();
  //紀錄並更新items(grid資料)
  const [items, setItems] = useState([]);

  //const { customFilterStandard } = require('./customFilterStandard').default;

  //紀錄並更新paginationOptions
  const [paginationOptionsState, setPaginationOptionsState] = useState(props.paginationOptions);

  //紀錄並更新paginationOptions
  const [tableLastState, setTableLastState] = useState({});

  const count = 0;

  const preLoading = (<Loading loading background="rgba(99,111,131,.5)" loaderColor="#321fdb" />);
  //呼叫的API位置
  const paginateUrl = props.paginateUrl;
  useEffect(() => {
    getQuery();

    //載入表格時先把copy id清除
    if (localStorage.getItem('copyId')) {
      localStorage.removeItem('copyId');
    }

    return function cleanup() {
      setPaginationOptionsState({});
      setItems([]);
      setTableLastState({});
      store.getState();
    }

  }, [count]);

  const t = props.translation;

  const ControlsButton = (props) => {
    const resetOnClick = props.clearFilterTrigger;
    const Controls = (props) => (<div className="table-controls row mb-1" {...props}></div>);
    const ResetFilterBtn = () => {
      return (
        <button className="btn btn-sm btn-info float-right ml-2 reset-filter" onClick={(e) => resetOnClick(e, tableLastState)}>
          <i className="fas fa-undo-alt mr-1"></i>
          {t('Reset filter')}
        </button>
      );
    };
    const InsertActionBtn = () => {
      return (
        <Link className="btn btn-sm btn-success float-right" to={location.pathname + '/add'} onClick={() => {
          localStorage.removeItem("copyId");
        }}>
          <i className="fas fa-plus-circle mr-1"></i>
          {t('Insert data')}
        </Link>
      );
    }
    if (typeof props.isFilterReset == 'undefined' && typeof props.isInsertAction == 'undefined') {
      return <></>;
    }

    if (typeof props.isFilterReset !== 'undefined' && typeof props.isInsertAction === 'undefined') {
      return (
        <Controls>
          <div className="col">
            <ResetFilterBtn />
          </div>
        </Controls>
      );
    }

    if (typeof props.isFilterReset === 'undefined' && typeof props.isInsertAction !== 'undefined') {
      return (
        <Controls>
          <div className="col">
            <InsertActionBtn {...props} />
          </div>
        </Controls>
      );
    }

    if (typeof props.isFilterReset !== 'undefined' && typeof props.isInsertAction !== 'undefined') {
      return (
        <Controls>
          <div className="col">
            <ResetFilterBtn {...props} />
            <InsertActionBtn {...props} />
          </div>
        </Controls>
      );
    }
  }
  const pathname = location.pathname.replace('^\/', '');
  const pathnameSplit = pathname.split('/');

  const getQuery = (type, params, success, error) => {
    document.getElementById('loading-background').classList.remove('d-none');
    if (props.apiOther) {
      if (!params) {
        params = {};
      }
      params.apiOther = props.apiOther
    }
    dispatch({ type: 'apiGet', url: paginateUrl, params: params });
    store.getState().then(function(dispatcher) {
      const data = dispatcher.apiGet.data;
      const code = dispatcher.apiGet.code;
      if (code == 0) {
        const mergeOptions = {
          totalSize: data.pages.totalItemCount,
          sizePerPage: data.pages.itemCountPerPage,
          page: data.pages.current
        };
        setPaginationOptionsState((paginationOptionsState) => ({ ...paginationOptionsState, ...mergeOptions }));

        setItems(() => {
          const items = data.items;
          return items;
        });
        if (typeof success === 'function') {
          success();
        }
        if (type == 'pagination') {
          animateScrollTo(document.querySelector('.c-wrapper'));
        }
      } else {
        if (code == -2) {
          notify('error', t('admin-session-fail'), 3, () => {
            const redirectPath =  `/${pathnameSplit[0]}/${SYS_LANG}/admin-login`.replace(/\/{2,}/, '/');
            location.href = redirectPath;
          });
        }
        //錯誤處理
        if (typeof error === 'function') {
          error();
        }
      }
      document.getElementById('loading-background').classList.add('d-none');
    }).catch(function(error) {
      console.log(error);
      document.getElementById('loading-background').classList.add('d-none');
    });

  };

  const putApi = (params, done) => {
    document.getElementById('loading-background').classList.remove('d-none');
    dispatch({ type: 'apiPut', url: paginateUrl, params: params.cellEdit });
    store.getState().then(function(dispatcher) {
      const code = dispatcher.apiPut.code;
      if (code === 0) {
        delete params.cellEdit;
        getQuery(null, params);
        notify('success', t('update success'));
        if (typeof done == 'function') {
          done(true);
        }

      } else {
        if (code == -2) {
          notify('error', t('admin-session-fail'), 3, () => {
            location.href = `/${SYS_LANG}/admin-login`;
          });
        }
        //const errorNotify = dispatch.payload.response.data.notify.join("");
        notify('error', t('update fail'), 3, () => {
          getQuery(null, tableLastState);
        });
        if (typeof done == 'function') {
          done(false);
        }

      }
      document.getElementById('loading-background').classList.add('d-none');
    }).catch(function(error) {
      console.error(error);
      if (typeof done == 'function') {
        done(false);
      }
      document.getElementById('loading-background').classList.add('d-none');
    });
  }

  const onTableChange = function(type, newState, done) {
    const { page, sizePerPage, filters, sortField, sortOrder, cellEdit/*, data*/ } = newState;
    //let searahVerify = 0;
    setTableLastState(newState);
    let params = {
      page: page,
      item_count_per_page: sizePerPage,
    };

    if (sortField && sortOrder) {
      params.sort = [sortField, sortOrder];
    }

    if (filters) {
      let _filters_ = {};
      const filterKeys = Object.keys(filters);
      filterKeys.forEach((key) => {
        var filter = filters[key];
        if (typeof filter.filterVal != 'undefined' && (filter.filterType == 'NUMBER' || filter.filterType == 'DATE')) {
          switch (filter.filterType) {
            case 'NUMBER':
              if (typeof filter.filterVal.number != 'undefined' && filter.filterVal.number.length && filter.filterVal.comparator.length > 0) {
                _filters_[key] = filters[key];
              }
              break;
            case 'DATE':
              if (filter.filterVal.date && filter.filterVal.comparator) {
                _filters_[key] = filters[key];
              }
              break;
          }
        } else {
          _filters_[key] = filters[key];
        }
      });
      params.filters = _filters_;
    }
    if (cellEdit) {
      if (type == 'agreeCellEdit') {
        params.cellEdit = cellEdit;
        putApi(params, done);
      }
      return;
    } else {
      /*
      if (typeof params.filters != 'undefined') {
          //先針對email、full_name(姓名)、cellphone手機、zip(郵遞區號)、county(縣市)、distinct(鄉鎮市區)、address(住址)
          Object.keys(params.filters).forEach((key) => {
              if (typeof customFilterStandard[key] !== 'undefined') {
                  if (typeof params.filters[key].filterVal !== 'undefined') {
                      let matcher = params.filters[key].filterVal.match(customFilterStandard[key]);
                      if (matcher) {
                          searahVerify += 1
                      }
                  }
              } else {
                  searahVerify += params.filters[key].filterVal.length > 0 ? 1 : 0;
              }
          });
      }
      */
      //if (searahVerify) {
      getQuery(type, params);
      //}
    }

  }

  const correctPorps = ["columns", "filter", "filterPosition", "selectRow"];
  let dynamicProps = {};
  Object.keys(props).filter((key) => {
    if (correctPorps.indexOf(key) > -1) {
      dynamicProps[key] = props[key];
    }
  });

  //紀錄選取狀態
  const [checkedsState, setCheckedsState] = useState([]);

  //選取的checkbox
  const onSelect = (row, isSelect) => {
    let _checkedsState = checkedsState;
    if (isSelect) {
      _checkedsState.push(row);
      setCheckedsState(_checkedsState);
    } else {
      const index = _checkedsState.indexOf(row);
      checkedsState.splice(index, 1);
      setCheckedsState(_checkedsState);
    }
  };

  const onSelectAll = (isSelect, rows) => {
    if (isSelect) {
      setCheckedsState(rows);
    } else {
      setCheckedsState([]);
    }
  };

  if (typeof props.isSelectRow === 'boolean') {
    dynamicProps.selectRow = {
      mode: 'checkbox',
      onSelect: onSelect,
      onSelectAll: onSelectAll,
    };
  }


  const deleteApi = (params) => {
    dispatch({ type: 'apiDelete', url: paginateUrl, params: params });
    document.getElementById('loading-background').classList.remove('d-none');
    store.getState().then(function(dispatcher) {
      const code = dispatcher.apiDelete.code;
      if (typeof code == 'number' && code === 0) {
        getQuery(null, tableLastState);
        onTableChange('delete', tableLastState);
        notify('success', t('delete success'), () => {
          getQuery(null, tableLastState);
        });
        setCheckedsState([]);
      } else {
        if (code == -2) {
          notify('error', t('admin-session-fail'), 3, () => {
            location.href = `${pathnameSplit[0]}/admin/login`;
          });
        }
        //錯誤訊息
        const errorNotify = dispatcher.apiDelete.notify.join("");
        if (errorNotify) {
          notify('error', errorNotify, 3, () => {
            getQuery(null, tableLastState);
          });
        } else {
          notify('error', t('delete fail'), 3, () => {
            getQuery(null, tableLastState);
          });

        }
      }
      document.getElementById('loading-background').classList.add('d-none');
    }).catch(function(error) {
      //錯誤訊息
      console.error(error);
      document.getElementById('loading-background').classList.add('d-none');
    });
  }
  const onDelete = (e) => {
    e.preventDefault();
    if (checkedsState.length) {
      toConfirm(() => { deleteApi(checkedsState) }, t);
    }
  }

  const bootstrapTable = useRef('bootstrapTable');
  let bootstrapTableProps = {
    bootstrap4: true,
    remote: true,
    keyField: 'id',
    filterPosition: 'top',
    wrapperClasses: 'table-responsive-lg',
    striped: true,
    hover: true,
    data: items,
    //pagination: paginationFactory(paginationOptionsState)
  };
  if(typeof props.noPagination == 'undefined') {
    bootstrapTableProps.pagination = paginationFactory(paginationOptionsState);
  }
  return (
    <Suspense fallback={preLoading}>
      <ControlsButton {...props} />
      <BootstrapTable
        classes="table-rwd"
        filtersClasses="filters-wrapper"
        {...bootstrapTableProps}
        filter={filterFactory()}
        onTableChange={onTableChange}
        {...dynamicProps}
        ref={bootstrapTable}
        cellEdit={cellEditFactory({
          mode: 'dbclick',
          blurToSave: true,
          beforeSaveCell: (oldValue, newValue, row, column) => {
            if (newValue != oldValue) {
              const filed = column.dataField;
              row[filed] = newValue;
              let newState = tableLastState;
              if (Object.keys(tableLastState).length === 0) {
                newState = {
                  sortOrder: null,
                  cellEdit: { rowId: row.id, dataField: column.dataField, newValue: newValue },
                  sortField: null,
                  page: 1,
                  sizePerPage: 25,
                  searchText: null,
                  cellEdit: { rowId: row.id, dataField: column.dataField, newValue: newValue },
                  data: items,
                };
              } else {
                newState.cellEdit = { rowId: row.id, dataField: column.dataField, newValue: newValue };
              }
              onTableChange('agreeCellEdit', newState);
            }
          },
        })}
      />
      {
        (typeof props.isSelectRow == 'boolean' && props.isSelectRow == true) &&
        //修正版面不協調的問題(沒資料時按鈕會跟上面的原件黏在一起)
        <button className="btn btn-danger btn-sm mt-3" onClick={onDelete}>
          <i className="fas fa-trash-alt mr-1" />
          {t('Batch delete')}
        </button>
      }
    </Suspense>
  );

}

export default AdminBootstrapTable;