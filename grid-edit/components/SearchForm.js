import React from 'react';

function getCurrentDate() {
  let trailingZero = (x) => { return x < 10 ? `0${x}` : x };
  let today = new Date();
  let day  = trailingZero(today.getDate());
  let month = trailingZero(today.getMonth() + 1);
  let year = today.getFullYear();

  return `${day}/${month}/${year}`;
}

class SearchForm extends React.Component {
  constructor(props) {
    super(props);

    this.state = {
      fromDate: getCurrentDate(),
      toDate: getCurrentDate()
    }
  }
  componentDidMount() {
    $('.datepicker').datepicker({
      format: 'dd/mm/yyyy',
      language: 'es',
      orientation: "top right",
      todayHighlight: true,
      autoclose: true
    });
  }
  handleFormSubmit(e) {
    e.preventDefault();

    if (this.props.onSearch) {
      this.props.onSearch({
        fromDate: this.state.fromDate,
        toDate: this.state.toDate,
        clientId: this.refs.clients.value
      });
    }
  }
  render() {
    return <form onSubmit={this.handleFormSubmit.bind(this)}>
      <div className="row">
        <div className="col-md-8">
          <div className="form-group">
            <label>Cliente</label>
            <select className="form-control" ref="clients">
              {
                this.props.clients.map((client, idx) =>
                  <option key={idx} value={client.id}>
                    {client.name}
                  </option>
                )
              }
            </select>
          </div>
        </div>
      </div>
      <div className="row">
        <div className="col-md-4">
          <div className="form-group">
            <label>Desde Fecha</label>
            <input type="text"
              className="form-control datepicker"
              onChange={(e) => this.setState({fromDate: e.target.value})}
              value={this.state.fromDate} />
          </div>
        </div>
        <div className="col-md-4">
          <div className="form-group">
            <label>Hasta Fecha</label>
            <input type="text"
              className="form-control datepicker"
              onChange={(e) => this.setState({toDate: e.target.value})}
              value={this.state.toDate}/>
          </div>
        </div>
      </div>
      <div className="row">
        <div className="col-md-2">
          <div className="form-group">
            <button type="submit" className="btn btn-primary">
              <i className="fa fa-search"></i>&nbsp;
              Buscar
            </button>
          </div>
        </div>
      </div>
    </form>;
  }
}

export default SearchForm;
