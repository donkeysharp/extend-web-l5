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
      searchData: {}
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
    this.setState({
      news: Object.assign({}, news, { isFetching: true }),
      searchData: data
    });
    $http.get('/grid/news', data).then((res) => {
      let news = this.state.news;
      let newState = {
        isFetching: false,
        data: res
      };
      this.setState({news: Object.assign({}, news, newState)});
    });
  }
  handleOnCellChange(index, updated) {
    let news = this.state.news.data;

    if ('code' in updated || 'clasification' in updated) {
      news[index].news = Object.assign({}, news[index].news, updated);
    } else {
      news[index] = Object.assign({}, news[index], updated);
    }

    this.setState({
      news: {
        isFetching: false,
        data: news
      }
    });
  }
  handleOnSaveClick() {
    console.log(this.state.news.data);
    $http.put('/grid/news', this.state.news.data).then((res) => {
      $http.get('/grid/news', this.state.searchData).then((res) => {
        let news = this.state.news;
        let newState = {
          isFetching: false,
          data: res
        };
        this.setState({news: Object.assign({}, news, newState)});
      });
    });

  }
  getContentBody() {
    if (this.state.news.isFetching) {
      return <Spinner />
    } else if (this.state.news.data.length !== 0) {
      return (
        <NewsGrid
          onCellChange={this.handleOnCellChange.bind(this)}
          onSaveClick={this.handleOnSaveClick.bind(this)}
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
