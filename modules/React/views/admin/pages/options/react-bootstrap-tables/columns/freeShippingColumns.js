import React from 'react';
import DecimalRenderer from './components/DecimalRenderer';

const freeShippingColumns = (t, smColumn) => {
  let fullColumns = [
    {
      dataField: 'id',
      text: 'id',
      hidden: true,
    },
    {
      dataField: 'language_id',
      text: t('columns-language_id'),
      hidden: true,
    },
    {
      dataField: 'locale_id',
      text: t('columns-locale_id'),
      hidden: true,
    },
    {
      dataField: 'display_name',
      text: t('Display name'),
      sort: false,
      editable: false,
    },
    {
      dataField: 'name',
      text: t('columns-name'),
      sort: false,
      editable: false,
    },
    {
      dataField: 'target_value',
      text: t('columns-target_value'),
      sort: false,
      editable: true,
      editorRenderer: (editorProps, value, row, column, rowIndex, columnIndex) => {
        return (<DecimalRenderer {...editorProps} value={parseFloat(value)} />);
      },
    },
  ];
  return fullColumns;
};


export default freeShippingColumns;