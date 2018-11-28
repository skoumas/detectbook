import React from 'react';

import './Comment.css';

class Comment extends React.Component {

	constructor(props) {
		super();
		this.state = {
			edit: false,
			editContent: ""
		}
		this.edit = this.edit.bind(this);
		this.editSubmit = this.editSubmit.bind(this);
		this.delete = this.delete.bind(this);
	}

	componentDidMount(props) {

	}

	edit() {
		this.setState({
			edit:true,
			editContent: this.props.comment.content
		});
	}

	editSubmit() {
		fetch("http://0.0.0.0:8100/api/comments/" + this.props.comment.id + "/update" ,{
			method: "POST",
			body: JSON.stringify({
				"_token": this.props.user.token,
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
					this.props.comment.content = this.state.editContent;

					this.props.app.setState({
						errorMessage: "Updated!"
					})

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
		fetch("http://0.0.0.0:8100/api/comments/" + this.props.comment.id + "/delete" ,{
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
			<div className="comment">
				{this.state.edit &&
					<div>
						<div className="form-group">
							<label htmlFor="exampleInputPassword1">Edit Comment</label>
							<textarea  className="form-control" onChange={ (e) => this.setState({ editContent: e.target.value }) }>
								{this.state.editContent}
							</textarea>
						</div>
						<p>
							<button onClick={this.editSubmit}>Save</button>
						</p>
					</div>
				}
				{!this.state.edit &&
					<p>
						{ this.props.comment.content }
					</p>
			 	}
				<hr/>
					<div>
						{ this.props.comment.user.name } |
						{ this.props.comment.created_at } 
						{(this.props.app.state.logged) &&
							<span>
								{(this.props.comment.user.id===this.props.user.id) &&
									<span> | <button className="btn btn-primary" onClick={this.edit}>Edit</button> | <button className="btn btn-danger" onClick={this.delete}>Delete</button> </span>
								}
							</span>

						}
					</div>
			</div>
		)
	}
}
export default Comment;
