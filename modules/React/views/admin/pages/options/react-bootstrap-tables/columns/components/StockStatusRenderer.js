import React from 'react';
import PropTypes from 'prop-types';

class StockStatusRenderer extends React.Component {

  static propTypes = {
    onUpdate: PropTypes.func.isRequired
  }

  constructor(props) {
    super(props);
    this.onChange = this.onChange.bind(this);
    this.state = {
      stock_status_container: null,
      selected: 0,
    }
  }

  componentDidMount() {
    this.stock_status.focus();
    this.setState({
      stock_status_container: pageConfig.stock_status,
      selected: this.props.value
    });
  }
  componentWillUnmount() {
    this.stock_status.blur();
  }
  getValue() {
    return parseInt(this.stock_status.value, 10);
  }

  onChange() {
    //this.stock_status.value = this.stock_status.value;
    this.setState((state) => {
      return {
        ...state,
        selected: this.stock_status.value,
      }
    });
    this.stock_status.blur();
  }


  render() {
    const { t, value, onUpdate, className, rowIndex, columnIndex, ...rest } = this.props;
    return (
      <select
        {...rest}
        defaultValue={this.state.selected}
        onChange={this.onChange}
        className="custom-select custom-select-sm"
        key="stock_status"
        ref={(node) => { this.stock_status = node; }}
      >
        {
          this.state.stock_status_container && this.state.stock_status_container.map((item, key) => {
            return (<option key={rowIndex + '-' + columnIndex + '-' + key} value={item.value}>{t(item.label)}</option>);
          })
        }
      </select>
    );
  }
}

export default StockStatusRenderer;