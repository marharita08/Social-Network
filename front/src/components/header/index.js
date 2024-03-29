import React, {useState} from "react";
import {Link} from "react-router-dom";
import PropTypes from "prop-types";
import {Avatar, Menu, MenuItem} from "@mui/material";
import HomeIcon from '@mui/icons-material/Home';
import PersonIcon from '@mui/icons-material/Person';
import LogoutIcon from '@mui/icons-material/Logout';
import NoteAddIcon from '@mui/icons-material/NoteAdd';
import env from "../../config/envConfig";

const Header = ({handleClickOpen, user, authenticated, logout, isAdmin}) => {
    const [anchorEl, setAnchorEl] = useState(null);

    const handleMenu = (event) => {
        event.preventDefault();
        setAnchorEl(event.currentTarget);
    };

    const handleMenuClose = () => {
        setAnchorEl(null);
    };

    const handleLogout = () => {
        handleMenuClose();
        logout();
    }

    return (
        <>
            <header>
                <Link to={"/"}>
                    <button className={"left"} onClick={'update'}>
                        <div className={"inline"}>
                            <HomeIcon fontSize={"small"}/>
                        </div>
                        <div className={"inline margin"}>
                            Home
                        </div>
                    </button>
                </Link>
                {
                    authenticated &&
                    <div>
                        {
                            isAdmin &&
                            <Link to={"/all-articles"}>
                                <button onClick={'update'}>
                                    <div className={"inline"}>
                                        <HomeIcon fontSize={"small"}/>
                                    </div>
                                    <div className={"inline margin"}>
                                        All articles
                                    </div>
                                </button>
                            </Link>
                        }
                        <button onClick={handleClickOpen}>
                            <div className={"inline"}>
                                <NoteAddIcon fontSize={"small"}/>
                            </div>
                            <div className={"inline margin"}>
                                Add article
                            </div>
                        </button>
                        <button
                            className={"right"}
                            onClick={handleMenu}
                            style={{margin:0, border: "none", padding:'5px 10px'}}
                        >
                            <div className={'inline'}>
                                <Avatar
                                    src={`${env.apiUrl}${user.avatar}`}
                                    sx={{ width: 40,
                                        height: 40,
                                        margin: '0 5px'
                                    }}
                                />
                            </div>
                            <div className={'inline margin username'}>
                                {user.name}
                            </div>
                        </button>
                        <Menu
                            id="menu-article"
                            anchorEl={anchorEl}
                            anchorOrigin={{
                                vertical: 'top',
                                horizontal: 'right',
                            }}
                            keepMounted
                            transformOrigin={{
                                vertical: 'top',
                                horizontal: 'right',
                            }}
                            open={Boolean(anchorEl)}
                            onClose={handleMenuClose}
                        >
                            <Link to={`/profile/${user.user_id}`}
                                  onClick={'update'}
                                  style={{'textDecoration': 'none'}}
                            >
                                <MenuItem>
                                    <PersonIcon/>
                                    <div className={"margin"}>
                                        Profile
                                    </div>
                                </MenuItem>
                            </Link>
                            <Link to={"/"} style={{'textDecoration': 'none'}}>
                                <MenuItem onClick={handleLogout}>
                                    <LogoutIcon/>
                                    <div className={"margin"}>
                                        Logout
                                    </div>
                                </MenuItem>
                            </Link>
                        </Menu>
                    </div>
                }
            </header>
        </>
    );
}

Header.propTypes = {
    handleClickOpen: PropTypes.func,
    user: PropTypes.object,
    authenticated: PropTypes.bool.isRequired,
    logout: PropTypes.func,
    isAdmin: PropTypes.bool,
}

export default Header;
