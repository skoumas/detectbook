import React from 'react';
import './WriteComment.css';

class WriteComment extends React.Component {

	constructor(props) {
		super();
		this.state = {
			comment: "",
		}
		this.handleChange = this.handleChange.bind(this);
        this.keyPress = this.keyPress.bind(this);
     }

     handleChange(e) {
        this.setState({ comment: e.target.value });
     }

     keyPress(e){
		if(e.keyCode == 13){
			fetch("http://0.0.0.0:8100/api/comment/create" ,{
			   method: "POST",
			   body: JSON.stringify({
				   "_token": this.props.user.token,
				   "comment": this.state.comment,
				   "entry_id":this.props.entry.id
			   })
		   })
		   .then(res => res.json())
		   .then(
			   (result) => {
				   if (result.success) {
					   this.setState({
						    comment: ""
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
     }

	render() {
		return (
				<div>
				{this.props.app.state.logged &&
					<div class="writeComment">
						<textarea placeholder="Your comment..." onKeyDown={this.keyPress} onChange={this.handleChange}>{this.state.comment}</textarea>
					</div>
				}
				</div>
		)
	}
}
export default WriteComment;
