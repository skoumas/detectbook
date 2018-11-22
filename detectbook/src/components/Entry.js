import React from 'react';
import WriteComment from './WriteComment';
import Comment from './Comment';
import './Entry.css';

class Entry extends React.Component {

	constructor(props) {
		super();
		this.state = {
			edit: false,
			editTitle: "",
			editContent: ""
		}
		this.delete = this.delete.bind(this);
		this.edit = this.edit.bind(this);
		this.editSubmit = this.editSubmit.bind(this);
	}

	componentDidMount(props) {

	}

	edit() {
		this.setState({
			edit:true,
			editTitle: this.props.entry.title,
			editContent: this.props.entry.content
		});
	}

	editSubmit() {
		fetch("http://0.0.0.0:8100/api/entries/" + this.props.entry.id + "/update" ,{
			method: "POST",
			body: JSON.stringify({
				"_token": this.props.user.token,
				"title": this.state.editTitle,
				"content": this.state.editContent
			})
		})
		.then(res => res.json())
		.then(
			(result) => {
				console.log(result);
				if (result.success) {
					this.setState({
						edit: false
					});
					this.props.entry.title = this.state.editTitle;
					this.props.entry.content = this.state.editContent;
					this.props.app.setState({
						errorMessage: "Updated!"
					})
					this.props.app.loadComponents();
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

	delete() {

		fetch("http://0.0.0.0:8100/api/entries/" + this.props.entry.id + "/delete" ,{
			method: "POST",
			body: JSON.stringify({"_token": this.props.user.token})
		})
		.then(res => res.json())
		.then(
			(result) => {
				if (result.success) {
					this.props.app.setState({
						errorMessage: "Deleted!"
					})
					this.props.app.loadComponents();

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
				{this.state.edit &&
					<div class="card">
						<div className="card-body">
							<div className="form-group">
								<label htmlFor="exampleInputPassword1">Title</label>
								<input type="text" className="form-control" name="title" value={this.state.editTitle} onChange={ (e) => this.setState({ editTitle: e.target.value }) }/>
							</div>

							<div className="form-group">
									<label htmlFor="exampleInputPassword1">Comment</label>
									<textarea  className="form-control" onChange={ (e) => this.setState({ editContent: e.target.value }) }>
										{this.state.editContent}
									</textarea>
								</div>

							<p>
							<button onClick={this.editSubmit}>Save</button>
							</p>
						</div>
					</div>
				}
				{!this.state.edit &&
				<div className="card">
					<div className="card-body">
						<h4 className="card-title">{this.props.entry.title}</h4>
						<p className="card-text">{this.props.entry.content}</p>
						<p className="card-text">by: {this.props.entry.user.name} | {this.props.entry.updated_at}
						{this.props.app.state.logged &&
							<span>
							{(this.props.entry.user.id===this.props.user.id) &&
								<span> | <button className="btn btn-primary" onClick={this.edit}>Edit</button> | <button className="btn btn-danger" onClick={this.delete}>Delete</button> </span>
							}
							</span>
						}
						</p>
					</div>
				</div>
				}

				{this.props.entry.comments.map((comment, i) => {
					return (<Comment comment={comment}></Comment>)
				})}
				<WriteComment entry={this.props.entry} user={this.props.user} app={this.props.app}/>


			</div>
		)
	}
}
export default Entry;
