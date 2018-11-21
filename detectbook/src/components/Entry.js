import React from 'react';

import './Entry.css';

class Entry extends React.Component {

	constructor(props) {
		super();
		this.delete = this.delete.bind(this);
		this.edit = this.edit.bind(this);
	}

	componentDidMount(props) {

	}

	edit() {

	}

	delete() {
		fetch("http://0.0.0.0:8100/api/entries/" + this.props.entry.id + "/delete" ,{
			 headers: {"token": this.props.user.token}
		})
		.then(res => res.json())
		.then(
			(result) => {
				if (result.success) {
					this.loadComponents();
					this.setState({
						page:"guestbook"
					});
				} else {

				}
			},
			(error) => {
				this.setState({
					errorMessage: "Something went wrong. Please try again"
				});
			}
		)
	}


	render() {
		return (
			<div className="entry">
				<div className="card">
				  <div className="card-body">
				    <h4 className="card-title">{this.props.entry.title}</h4>
				    <p className="card-text">{this.props.entry.content}</p>
					<p className="card-text">by: {this.props.entry.user.name} | {this.props.entry.created_at}
					{this.props.app.state.logged &&
						<span>
						{(this.props.entry.user.id===this.props.user.id) &&
							<span> | <button onClick={this.edit}>Edit</button> | <button onClick={this.delete}>Delete</button> </span>
						}
						</span>
					}
					</p>

				  </div>
				</div>
				<br/>
			</div>
		)
	}
}
export default Entry;
