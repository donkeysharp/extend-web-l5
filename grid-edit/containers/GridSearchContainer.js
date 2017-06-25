import React from 'react';

import SearchForm from '../components/SearchForm';
import NewsGrid from '../components/NewsGrid';
import Spinner from '../components/Spinner';
import { $http } from '../utils';


class GridSearchContainer extends React.Component {
  constructor(props) {
    super(props);
    this.state = {
      clients: [],
      media: [],
      sources: [],
      subtitles: [],
      topics: [],
      news: {
        isFetching: false,
        data: []
      },
    }
  }
  componentDidMount() {
    let extraParams = {
      clients: true,
      topics: true,
      media: true,
      subtitles: true,
      sources: true
    };
    $http.get('/news/extra', extraParams).then((res) => {
      this.setState({
        clients: res.clients,
        media: res.media,
        sources: res.sources,
        subtitles: res.subtitles,
        topics: res.topics
      });
    });
  }
  handleOnSearch(data) {
    let news = this.state.news;
    this.setState({news: Object.assign({}, news, { isFetching: true })})
    $http.get('/grid/news', data).then((res) => {
      let news = this.state.news;
      let newState = {
        isFetching: false,
        data: res
      };
      this.setState({news: Object.assign({}, news, newState)});
    })
  }
  getContentBody() {
    if (this.state.news.isFetching) {
      return <Spinner />
    } else if (this.state.news.data.length !== 0) {
      return (
        <NewsGrid
          clients={this.state.clients}
          media={this.state.media}
          sources={this.state.sources}
          subtitles={this.state.subtitles}
          topics={this.state.topics}
          news={this.state.news.data} />
      );
    }
    return <h4>No se encontraron noticias</h4>
  }
  render() {
    return <div className="row">
      <div className="col-md-10 col-md-offset-1">
        <div className="panel panel-dark">
          <div className="panel-heading"><b>Edición de Noticias (grilla)</b></div>
          <div className="panel-body">
            <SearchForm
              clients={this.state.clients}
              onSearch={this.handleOnSearch.bind(this)} />
            <br />
            { this.getContentBody() }
          </div>
        </div>
      </div>
    </div>
  }
}

export default GridSearchContainer;
